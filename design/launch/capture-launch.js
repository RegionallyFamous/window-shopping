#!/usr/bin/env node
const fs = require('node:fs/promises');
const path = require('node:path');
const os = require('node:os');
const { spawn, spawnSync } = require('node:child_process');

const themePath = path.resolve(__dirname, '../..');
const outRoot = path.join(themePath, 'design/launch/captures');
const baseUrl = 'http://localhost:8099';
const wpContainer = 'window-shopping-woo-test-wordpress';

const styles = ['studio', 'oddities', 'atelier', 'field-supply', 'pantry', 'signal'];
const productByStyle = {
  studio: { slug: 'velvet-utility-tote', query: 'signal' },
  oddities: { slug: 'pocket-thunder', query: 'signal' },
  atelier: { slug: 'ribbon-hem-shirt', query: 'signal' },
  'field-supply': { slug: 'camp-ledger-jacket', query: 'signal' },
  pantry: { slug: 'bottled-morning', query: 'signal' },
  signal: { slug: 'signal-wireless-headphones', query: 'signal' },
};

const viewports = {
  desktop: { width: 1440, height: 1040, mobile: false, deviceScaleFactor: 1 },
  mobile: { width: 390, height: 920, mobile: true, deviceScaleFactor: 2 },
};

function runDockerPhp(source) {
  const result = spawnSync('docker', ['exec', '-i', wpContainer, 'php'], {
    input: source,
    encoding: 'utf8',
    maxBuffer: 1024 * 1024 * 16,
  });
  if (result.status !== 0) {
    throw new Error((result.stderr || result.stdout || 'docker php failed').trim());
  }
  return result.stdout.trim();
}

function applyStyle(style) {
  return runDockerPhp(`<?php
require '/var/www/html/wp-load.php';
$style = '${style}';
$file = get_theme_file_path('styles/' . $style . '.json');
$data = json_decode(file_get_contents($file), true);
$data['isGlobalStylesUserThemeJSON'] = true;
global $wpdb;
$posts = get_posts(array(
  'post_type' => 'wp_global_styles',
  'post_status' => 'any',
  'numberposts' => 1,
  'tax_query' => array(array(
    'taxonomy' => 'wp_theme',
    'field' => 'name',
    'terms' => get_stylesheet(),
  )),
  'suppress_filters' => false,
));
$id = $posts ? $posts[0]->ID : wp_insert_post(wp_slash(array(
  'post_type' => 'wp_global_styles',
  'post_status' => 'publish',
  'post_name' => 'wp-global-styles-' . get_stylesheet(),
  'post_title' => $data['title'],
  'tax_input' => array('wp_theme' => array(get_stylesheet())),
)));
wp_update_post(wp_slash(array(
  'ID' => $id,
  'post_status' => 'publish',
  'post_title' => $data['title'],
  'post_date' => current_time('mysql'),
  'post_date_gmt' => current_time('mysql', 1),
)));
$wpdb->update($wpdb->posts, array(
  'post_content' => wp_json_encode($data),
  'post_name' => 'wp-global-styles-' . get_stylesheet(),
  'post_modified' => current_time('mysql'),
  'post_modified_gmt' => current_time('mysql', 1),
), array('ID' => $id));
clean_post_cache($id);
wp_set_post_terms($id, array(get_stylesheet()), 'wp_theme');
wp_cache_flush();
echo $data['title'];
`);
}

function productIdForSlug(slug) {
  const result = runDockerPhp(`<?php
require '/var/www/html/wp-load.php';
$post = get_page_by_path('${slug}', OBJECT, 'product');
if ( ! $post ) {
  fwrite(STDERR, 'Missing product ${slug}');
  exit(1);
}
echo $post->ID;
`);
  return Number.parseInt(result, 10);
}

function createOrderUrl(productId) {
  return runDockerPhp(`<?php
require '/var/www/html/wp-load.php';
$order = wc_create_order(array('created_via' => 'window-shopping-launch-qa'));
$product = wc_get_product(${productId});
if (!$product) {
  fwrite(STDERR, 'Missing product ${productId}');
  exit(1);
}
$order->add_product($product, 1);
$address = array(
  'first_name' => 'Launch',
  'last_name' => 'Customer',
  'email' => 'launch@example.com',
  'phone' => '555-0100',
  'address_1' => '1 Storefront Lane',
  'city' => 'Chicago',
  'state' => 'IL',
  'postcode' => '60601',
  'country' => 'US',
);
$order->set_address($address, 'billing');
$order->set_address($address, 'shipping');
$order->set_payment_method('cod');
$order->set_payment_method_title('Launch QA');
$order->calculate_totals();
$order->update_status('processing');
echo wc_get_endpoint_url('order-received', $order->get_id(), wc_get_checkout_url()) . '?key=' . $order->get_order_key();
`);
}

function launchChrome(port, userDataDir) {
  const chromePath = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
  return spawn(chromePath, [
    '--headless=new',
    `--remote-debugging-port=${port}`,
    `--user-data-dir=${userDataDir}`,
    '--disable-gpu',
    '--hide-scrollbars',
    '--no-first-run',
    '--no-default-browser-check',
    'about:blank',
  ], { stdio: ['ignore', 'ignore', 'ignore'] });
}

async function waitForChrome(port) {
  for (let i = 0; i < 80; i += 1) {
    try {
      const response = await fetch(`http://127.0.0.1:${port}/json/version`);
      if (response.ok) return;
    } catch (_) {}
    await new Promise((resolve) => setTimeout(resolve, 250));
  }
  throw new Error('Chrome did not start');
}

async function openCdpPage(port) {
  const response = await fetch(`http://127.0.0.1:${port}/json/new?about:blank`, { method: 'PUT' });
  const target = await response.json();
  const ws = new WebSocket(target.webSocketDebuggerUrl);
  await new Promise((resolve, reject) => {
    ws.addEventListener('open', resolve, { once: true });
    ws.addEventListener('error', reject, { once: true });
  });

  let id = 0;
  const pending = new Map();
  const events = new Map();

  ws.addEventListener('message', (event) => {
    const message = JSON.parse(event.data);
    if (message.id && pending.has(message.id)) {
      const { resolve, reject } = pending.get(message.id);
      pending.delete(message.id);
      if (message.error) reject(new Error(message.error.message));
      else resolve(message.result || {});
      return;
    }
    if (message.method && events.has(message.method)) {
      for (const listener of events.get(message.method)) listener(message.params || {});
      events.delete(message.method);
    }
  });

  function send(method, params = {}) {
    const message = { id: ++id, method, params };
    ws.send(JSON.stringify(message));
    return new Promise((resolve, reject) => {
      pending.set(message.id, { resolve, reject });
      setTimeout(() => {
        if (pending.has(message.id)) {
          pending.delete(message.id);
          reject(new Error(`CDP timeout: ${method}`));
        }
      }, 60000);
    });
  }

  function waitEvent(method, timeout = 30000) {
    return new Promise((resolve) => {
      const listeners = events.get(method) || [];
      listeners.push(resolve);
      events.set(method, listeners);
      setTimeout(() => resolve({ timeout: true }), timeout);
    });
  }

  await send('Page.enable');
  await send('Runtime.enable');
  await send('Network.enable');

  return { send, waitEvent, close: () => ws.close() };
}

async function capturePage(cdp, url, file, viewport, options = {}) {
  await cdp.send('Emulation.setDeviceMetricsOverride', viewport);
  if (options.clearCookies) {
    await cdp.send('Network.clearBrowserCookies');
  }
  const load = cdp.waitEvent('Page.loadEventFired', 22000);
  await cdp.send('Page.navigate', { url });
  await load;
  await cdp.send('Runtime.evaluate', {
    expression: `new Promise(resolve => setTimeout(resolve, ${options.wait || 4200}))`,
    awaitPromise: true,
  });
  await cdp.send('Runtime.evaluate', {
    expression: `(() => {
      document.documentElement.style.scrollBehavior = 'auto';
      document.body.style.scrollBehavior = 'auto';
      window.scrollTo({ left: 0, top: 0, behavior: 'instant' });
      return new Promise(resolve => setTimeout(resolve, 260));
    })()`,
    awaitPromise: true,
  });

  if (options.warmPage !== false) {
    await cdp.send('Runtime.evaluate', {
      expression: `new Promise(async (resolve) => {
        const sleep = (delay) => new Promise((done) => setTimeout(done, delay));
        document.documentElement.style.scrollBehavior = 'auto';
        document.body.style.scrollBehavior = 'auto';
        const startX = 0;
        const startY = 0;
        const pageHeight = Math.max(
          document.body ? document.body.scrollHeight : 0,
          document.documentElement ? document.documentElement.scrollHeight : 0
        );
        const step = Math.max(280, Math.floor(window.innerHeight * 0.72));

        for (const image of Array.from(document.images)) {
          image.loading = 'eager';
          image.decoding = 'async';
        }

        for (let y = 0; y <= pageHeight; y += step) {
          window.scrollTo({ left: 0, top: y, behavior: 'instant' });
          await sleep(180);
        }

        await Promise.race([
          Promise.allSettled(Array.from(document.images).map((image) => {
            if (image.complete && image.naturalWidth > 0) {
              return Promise.resolve();
            }

            return new Promise((done) => {
              const finish = () => done();
              image.addEventListener('load', finish, { once: true });
              image.addEventListener('error', finish, { once: true });
              setTimeout(finish, 1600);
            });
          })),
          sleep(2200),
        ]);

        window.scrollTo({ left: startX, top: startY, behavior: 'instant' });
        await sleep(260);
        resolve(true);
      })`,
      awaitPromise: true,
    });
    await cdp.send('Runtime.evaluate', {
      expression: `(() => {
        document.documentElement.style.scrollBehavior = 'auto';
        document.body.style.scrollBehavior = 'auto';
        window.scrollTo({ left: 0, top: 0, behavior: 'instant' });
        return new Promise(resolve => setTimeout(resolve, 260));
      })()`,
      awaitPromise: true,
    });
  }

  if (options.openMiniCart) {
    await cdp.send('Runtime.evaluate', {
      expression: `(() => {
        const button = document.querySelector('.wc-block-mini-cart__button');
        const drawer = document.querySelector('.wc-block-mini-cart__drawer, .wc-block-components-drawer');
        const isOpen = (() => {
          if (!drawer) return false;
          const rect = drawer.getBoundingClientRect();
          return rect.width > 0 && rect.right > window.innerWidth * 0.72 && rect.left < window.innerWidth * 0.28;
        })();

        if (button && !isOpen) button.click();
        return { hasButton: !!button, wasOpen: isOpen };
      })()`,
      awaitPromise: false,
    });
    await cdp.send('Runtime.evaluate', {
      expression: 'new Promise(resolve => setTimeout(resolve, 1800))',
      awaitPromise: true,
    });
  }

  const metrics = await cdp.send('Runtime.evaluate', {
    expression: `JSON.stringify((() => ({
      url: location.href,
      title: document.title,
      bodyClass: document.body.className,
      viewportWidth: document.documentElement.clientWidth,
      scrollWidth: document.documentElement.scrollWidth,
      overflowX: document.documentElement.scrollWidth > document.documentElement.clientWidth,
      activeStyleClass: Array.from(document.body.classList).find(c => c.startsWith('window-shopping-style-')) || '',
      hasMiniCartDrawer: !!document.querySelector('.wc-block-components-drawer__screen-overlay--with-slide-in, .wc-block-mini-cart__drawer'),
      notices: document.querySelectorAll('.wc-block-components-notice-banner, .woocommerce-message, .woocommerce-info, .woocommerce-error').length,
      imageCount: document.images.length,
      brokenImages: Array.from(document.images).filter(image => image.complete && image.naturalWidth === 0).length,
      unloadedImages: Array.from(document.images).filter(image => !image.complete || image.naturalWidth === 0).length
    }))())`,
    returnByValue: true,
  });

  const screenshot = await cdp.send('Page.captureScreenshot', {
    format: 'png',
    fromSurface: true,
    captureBeyondViewport: false,
  });
  await fs.mkdir(path.dirname(file), { recursive: true });
  await fs.writeFile(file, Buffer.from(screenshot.data, 'base64'));
  return JSON.parse(metrics.result.value || '{}');
}

function pagesForStyle(style) {
  const product = productByStyle[style];
  const productId = productIdForSlug(product.slug);
  const orderUrl = createOrderUrl(productId);
  return [
    { key: 'home', url: `${baseUrl}/`, clearCookies: true },
    { key: 'shop', url: `${baseUrl}/shop/`, clearCookies: true },
    { key: 'category', url: `${baseUrl}/product-category/${style}/`, clearCookies: true },
    { key: 'search', url: `${baseUrl}/?s=${encodeURIComponent(product.query)}&post_type=product`, clearCookies: true },
    { key: 'single', url: `${baseUrl}/product/${product.slug}/`, clearCookies: true },
    { key: 'cart', url: `${baseUrl}/cart/?add-to-cart=${productId}&quantity=1`, clearCookies: true },
    { key: 'checkout', url: `${baseUrl}/checkout/?add-to-cart=${productId}&quantity=1`, clearCookies: true },
    { key: 'order', url: orderUrl, clearCookies: true },
    { key: 'coming-soon', url: `${baseUrl}/coming-soon/`, clearCookies: true },
    { key: 'mini-cart', url: `${baseUrl}/?add-to-cart=${productId}&quantity=1`, openMiniCart: true, clearCookies: true },
  ];
}

async function main() {
  const selectedStyles = process.argv.slice(2).length ? process.argv.slice(2) : styles;
  const port = 9224 + Math.floor(Math.random() * 500);
  const userDataDir = await fs.mkdtemp(path.join(os.tmpdir(), 'window-shopping-chrome-'));
  const chrome = launchChrome(port, userDataDir);
  const results = [];

  try {
    await waitForChrome(port);
    const cdp = await openCdpPage(port);
    for (const style of selectedStyles) {
      console.log(`Applying ${style}`);
      applyStyle(style);
      const pages = pagesForStyle(style);
      for (const [viewportName, viewport] of Object.entries(viewports)) {
        for (const page of pages) {
          const file = path.join(outRoot, style, `${page.key}-${viewportName}.png`);
          console.log(`  ${viewportName} ${page.key}`);
          const result = await capturePage(cdp, page.url, file, viewport, {
            openMiniCart: page.openMiniCart,
            clearCookies: page.clearCookies,
            wait: page.key === 'checkout' || page.key === 'cart' ? 5200 : 4200,
          });
          results.push({
            style,
            viewport: viewportName,
            page: page.key,
            file: path.relative(themePath, file),
            ...result,
          });
        }
      }
    }
    cdp.close();
  } finally {
    chrome.kill('SIGTERM');
  }

  const reportPath = path.join(outRoot, 'capture-report.json');
  await fs.writeFile(reportPath, JSON.stringify(results, null, 2));
  const overflow = results.filter((item) => item.overflowX);
  console.log(`Wrote ${results.length} screenshots`);
  console.log(`Overflow findings: ${overflow.length}`);
  if (overflow.length) {
    for (const item of overflow) console.log(`  ${item.style} ${item.viewport} ${item.page}: ${item.scrollWidth}/${item.viewportWidth}`);
  }
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
