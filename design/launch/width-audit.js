#!/usr/bin/env node
const fs = require('node:fs/promises');
const path = require('node:path');
const os = require('node:os');
const { spawn, spawnSync } = require('node:child_process');

const themePath = path.resolve(__dirname, '../..');
const outRoot = path.join(themePath, 'design/launch/captures');
const outPath = path.join(outRoot, 'width-audit-report.json');
const baseUrl = process.env.WINDOW_SHOPPING_BASE_URL || 'http://localhost:8099';
const wpContainer = process.env.WINDOW_SHOPPING_WP_CONTAINER || 'window-shopping-woo-test-wordpress';

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
  tiny: { width: 360, height: 900, mobile: true, deviceScaleFactor: 2 },
  mobile: { width: 390, height: 920, mobile: true, deviceScaleFactor: 2 },
  tablet: { width: 768, height: 980, mobile: false, deviceScaleFactor: 1 },
  narrow: { width: 1024, height: 980, mobile: false, deviceScaleFactor: 1 },
  desktop: { width: 1440, height: 1040, mobile: false, deviceScaleFactor: 1 },
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
if (!$post) {
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
$order = wc_create_order(array('created_via' => 'window-shopping-width-audit'));
$product = wc_get_product(${productId});
if (!$product) {
  fwrite(STDERR, 'Missing product ${productId}');
  exit(1);
}
$order->add_product($product, 1);
$address = array(
  'first_name' => 'Width',
  'last_name' => 'Audit',
  'email' => 'width@example.com',
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
$order->set_payment_method_title('Width Audit');
$order->calculate_totals();
$order->update_status('processing');
echo wc_get_endpoint_url('order-received', $order->get_id(), wc_get_checkout_url()) . '?key=' . $order->get_order_key();
`);
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

function launchChrome(port, userDataDir) {
  const chromePath = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
  return spawn(chromePath, [
    '--headless=new',
    `--remote-debugging-port=${port}`,
    `--user-data-dir=${userDataDir}`,
    '--disable-gpu',
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
      }, 30000);
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

async function auditPage(cdp, page, viewport) {
  await cdp.send('Emulation.setDeviceMetricsOverride', viewport);
  if (page.clearCookies) {
    await cdp.send('Network.clearBrowserCookies');
  }

  const load = cdp.waitEvent('Page.loadEventFired', 16000);
  await cdp.send('Page.navigate', { url: page.url });
  await load;
  await cdp.send('Runtime.evaluate', {
    expression: 'new Promise(resolve => setTimeout(resolve, 1600))',
    awaitPromise: true,
  });

  if (page.openMiniCart) {
    await cdp.send('Runtime.evaluate', {
      expression: `(() => {
        const button = document.querySelector('.wc-block-mini-cart__button');
        if (button) button.click();
      })()`,
      awaitPromise: false,
    });
    await cdp.send('Runtime.evaluate', {
      expression: 'new Promise(resolve => setTimeout(resolve, 1200))',
      awaitPromise: true,
    });
  }

  const metrics = await cdp.send('Runtime.evaluate', {
    expression: `JSON.stringify((() => {
      const vw = document.documentElement.clientWidth;
      const visible = (el) => {
        const cs = getComputedStyle(el);
        const rect = el.getBoundingClientRect();
        if (cs.display === 'none' || cs.visibility === 'hidden' || rect.width <= 1 || rect.height <= 1) {
          return false;
        }

        if (el.closest('[hidden], [aria-hidden="true"], .screen-reader-text, .wc-block-components-address-form__address_2-toggle')) {
          return false;
        }

        const drawer = el.closest('.wc-block-mini-cart__drawer, .wc-block-components-drawer');
        if (drawer) {
          const drawerRect = drawer.getBoundingClientRect();
          const offCanvas = drawerRect.left >= vw - 2 || drawerRect.right <= 2;
          const open = drawer.getAttribute('aria-hidden') !== 'true' && !offCanvas;
          if (!open) return false;
        }

        if (el.matches('.zoomImg')) {
          return false;
        }

        return true;
      };
      const textLength = (el) => (el.innerText || el.textContent || '').replace(/\\s+/g, ' ').trim().length;
      const labelFor = (el) => {
        const classes = (el.className || '').toString().split(/\\s+/).filter(Boolean).slice(0, 4).join('.');
        const id = el.id ? '#' + el.id : '';
        const text = (el.innerText || el.textContent || '').replace(/\\s+/g, ' ').trim().slice(0, 90);
        return el.tagName.toLowerCase() + id + (classes ? '.' + classes : '') + (text ? ' | ' + text : '');
      };
      const minimumWidth = (group) => {
        if (vw < 600) {
          return {
            'product-card': 140,
            'wp-column': 280,
            'trust-item': 210,
            'checkout-reassurance-item': 210,
            'cart-main': 300,
            'cart-sidebar': 280,
            'product-summary': 300,
            'product-gallery': 300,
            'product-tabs': 290,
            'mini-cart': 300,
          }[group] || 220;
        }
        if (vw < 900) {
          return {
            'product-card': 210,
            'wp-column': 300,
            'trust-item': 220,
            'checkout-reassurance-item': 220,
            'cart-main': 420,
            'cart-sidebar': 300,
            'product-summary': 320,
            'product-gallery': 320,
            'product-tabs': 420,
            'mini-cart': 340,
          }[group] || 260;
        }

        return {
          'product-card': 220,
          'wp-column': 280,
          'trust-item': 210,
          'checkout-reassurance-item': 220,
          'cart-main': 520,
          'cart-sidebar': 300,
          'product-summary': 360,
          'product-gallery': 420,
          'product-tabs': 540,
          'mini-cart': 360,
        }[group] || 260;
      };
      const groups = [
        ['product-card', '.wc-block-product, .wp-block-post.product'],
        ['wp-column', '.wp-block-column'],
        ['trust-item', '.ws-trust-item'],
        ['checkout-reassurance-item', '.ws-checkout-reassurance__item'],
        ['cart-main', '.wc-block-cart__main, .wc-block-components-main, .wc-block-checkout__main'],
        ['cart-sidebar', '.wc-block-cart__sidebar, .wc-block-checkout__sidebar, .wc-block-components-sidebar'],
        ['product-summary', '.ws-product-summary'],
        ['product-gallery', '.wp-block-woocommerce-product-gallery, .wp-block-woocommerce-product-image-gallery, .woocommerce-product-gallery'],
        ['product-tabs', '.wp-block-woocommerce-product-details, .woocommerce-tabs.wc-tabs-wrapper'],
        ['mini-cart', '.wc-block-mini-cart__drawer, .wc-block-components-drawer'],
      ];
      const findings = [];
      const hasDocumentOverflow = document.documentElement.scrollWidth > vw + 4;

      for (const [group, selector] of groups) {
        for (const el of Array.from(document.querySelectorAll(selector))) {
          if (!visible(el)) continue;
          if (group === 'product-card' && el.closest('.ws-window-display, .is-style-showcase-hero')) {
            continue;
          }
          if (group === 'wp-column' && el.matches('.ws-filter-rail')) {
            continue;
          }
          const rect = el.getBoundingClientRect();
          const len = textLength(el);
          const min = minimumWidth(group);
          if (group === 'wp-column' && el.closest('.ws-product-dossier__grid') && rect.width >= 260) {
            continue;
          }
          if ((group === 'trust-item' || group === 'checkout-reassurance-item') && rect.width >= 180) {
            continue;
          }
          if (rect.width < min && len > 18) {
            findings.push({
              type: 'skinny-column',
              group,
              width: Math.round(rect.width),
              height: Math.round(rect.height),
              min,
              label: labelFor(el),
            });
          }
        }
      }

      for (const el of Array.from(document.querySelectorAll('h1,h2,h3,.wp-block-post-title,.wc-block-components-product-name,.wp-block-button__link,.wc-block-components-button,button,.tabs.wc-tabs a'))) {
        if (!visible(el)) continue;
        const rect = el.getBoundingClientRect();
        const len = textLength(el);
        if (el.scrollWidth > el.clientWidth + 2) {
          findings.push({
            type: 'text-overflow',
            width: Math.round(rect.width),
            scrollWidth: el.scrollWidth,
            label: labelFor(el),
          });
        }
        const buttonLike = el.matches('.wp-block-button__link, .wc-block-components-button, button');
        const productName = el.matches('.wc-block-components-product-name');
        const crampedButton = buttonLike && el.scrollWidth > el.clientWidth + 2;
        const crampedProductName = productName && rect.width < 120 && len > 24;
        if (vw >= 600 && len > 18 && (crampedButton || crampedProductName)) {
          findings.push({
            type: 'narrow-text-control',
            width: Math.round(rect.width),
            label: labelFor(el),
          });
        }
      }

      if (hasDocumentOverflow) {
        const overflowRoots = new Set();
        for (const el of Array.from(document.querySelectorAll('body *'))) {
          if (!visible(el)) continue;
          const rect = el.getBoundingClientRect();
          if (rect.left < -2 || rect.right > vw + 2) {
            const root = el.closest('.wp-block-woocommerce-cart, .wp-block-woocommerce-checkout, .wp-block-woocommerce-product-gallery, .wp-block-woocommerce-product-details, .ws-product-layout, .ws-hero, .ws-trust-band, .ws-page-shell') || el;
            if (overflowRoots.has(root)) continue;
            overflowRoots.add(root);
            findings.push({
              type: 'element-overflow-x',
              width: Math.round(rect.width),
              left: Math.round(rect.left),
              right: Math.round(rect.right),
              label: labelFor(root),
            });
          }
        }
      }

      return {
        viewportWidth: vw,
        scrollWidth: document.documentElement.scrollWidth,
        overflowX: document.documentElement.scrollWidth > vw + 1,
        activeStyleClass: Array.from(document.body.classList).find((className) => className.startsWith('window-shopping-style-')) || '',
        findings: findings.slice(0, 40),
      };
    })())`,
    returnByValue: true,
  });

  return JSON.parse(metrics.result.value || '{}');
}

async function main() {
  const selectedStyles = process.argv.slice(2).length ? process.argv.slice(2) : styles;
  const port = 9650 + Math.floor(Math.random() * 300);
  const userDataDir = await fs.mkdtemp(path.join(os.tmpdir(), 'ws-width-audit-'));
  const chrome = launchChrome(port, userDataDir);
  const results = [];

  try {
    await waitForChrome(port);
    const cdp = await openCdpPage(port);
    for (const style of selectedStyles) {
      console.log(`Applying ${style}: ${applyStyle(style)}`);
      const pages = pagesForStyle(style);
      for (const [viewportName, viewport] of Object.entries(viewports)) {
        for (const page of pages) {
          const metrics = await auditPage(cdp, page, viewport);
          const findingCount = (metrics.findings || []).length + (metrics.overflowX ? 1 : 0);
          if (findingCount) {
            console.log(`  ${viewportName} ${page.key}: ${findingCount} width findings`);
          }
          results.push({
            style,
            viewport: viewportName,
            page: page.key,
            url: page.url,
            ...metrics,
          });
        }
      }
    }
    cdp.close();
  } finally {
    chrome.kill('SIGTERM');
  }

  await fs.mkdir(outRoot, { recursive: true });
  await fs.writeFile(outPath, JSON.stringify(results, null, 2));

  const bad = results.filter((item) => item.overflowX || (item.findings || []).length);
  const summary = new Map();
  for (const item of bad) {
    for (const finding of item.findings || []) {
      const key = `${finding.type}|${finding.group || ''}|${item.viewport}|${item.page}`;
      summary.set(key, (summary.get(key) || 0) + 1);
    }
  }

  console.log(`Width audit pages: ${results.length}`);
  console.log(`Width audit findings: ${bad.length}`);
  console.log(path.relative(themePath, outPath));
  for (const [key, count] of Array.from(summary.entries()).sort((a, b) => b[1] - a[1]).slice(0, 30)) {
    console.log(`  ${count} ${key}`);
  }
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
