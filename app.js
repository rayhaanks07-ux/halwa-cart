/**
 * Halwa Haweli — Authentic Kerala Halwa Application Engine
 * Pure Vanilla JavaScript & REST API Integration
 */

(function () {
  'use strict';

  const DEFAULT_PRODUCTS = [
    {
      id: "halwa-dates",
      name: "Dates Halwa - Authentic, Kerala Halwa",
      category: "single",
      discount_badge: "55.06% OFF",
      badge_type: "Sale",
      is_sold_out: false,
      rating: 5.0,
      review_count: 3,
      original_price: 599.00,
      base_price: 299.00,
      image: "assets/images/gajar_halwa.jpg",
      weights: [
        { size: "200 g", price: 299.00, orig: 599.00 },
        { size: "500 g", price: 549.00, orig: 999.00 },
        { size: "1 kg", price: 999.00, orig: 1899.00 }
      ]
    },
    {
      id: "halwa-black",
      name: "Black Halwa - Authentic, Kerala Halwa",
      category: "single",
      discount_badge: "50.06% OFF",
      badge_type: "Sale",
      is_sold_out: false,
      rating: 5.0,
      review_count: 19,
      original_price: 499.00,
      base_price: 299.00,
      image: "assets/images/omani_halwa.jpg",
      weights: [
        { size: "200 g", price: 299.00, orig: 499.00 },
        { size: "500 g", price: 499.00, orig: 899.00 },
        { size: "1 kg", price: 899.00, orig: 1599.00 }
      ]
    },
    {
      id: "halwa-jackfruit",
      name: "Jackfruit Halwa - Authentic, Kerala Halwa",
      category: "fruit",
      discount_badge: "50.07% OFF",
      badge_type: "Sale",
      is_sold_out: false,
      rating: 5.0,
      review_count: 50,
      original_price: 699.00,
      base_price: 349.00,
      image: "assets/images/sooji_halwa.jpg",
      weights: [
        { size: "200 g", price: 349.00, orig: 699.00 },
        { size: "500 g", price: 599.00, orig: 1199.00 },
        { size: "1 kg", price: 1099.00, orig: 1999.00 }
      ]
    },
    {
      id: "halwa-fruit-nut",
      name: "Fruit And Nut Halwa - Authentic, Kerala Halwa",
      category: "fruit",
      discount_badge: "50.07% OFF",
      badge_type: "Sale",
      is_sold_out: false,
      rating: 5.0,
      review_count: 7,
      original_price: 699.00,
      base_price: 349.00,
      image: "assets/images/karachi_halwa.jpg",
      weights: [
        { size: "200 g", price: 349.00, orig: 699.00 },
        { size: "500 g", price: 599.00, orig: 1199.00 },
        { size: "1 kg", price: 1099.00, orig: 1999.00 }
      ]
    }
  ];

  const DEFAULT_BANNERS = [
    {
      id: "banner-festive-sale",
      tag: "🔥 55.06% OFF • FESTIVE SALE",
      title: "Authentic Kozhikode",
      highlight: "Kerala Halwa",
      subtext: "Slow-simmered in pure coconut oil, natural dates, and rich jaggery, loaded with whole roasted cashews.",
      cta_text: "Shop Dates & Black Halwa",
      cta_link: "#single-varieties",
      image: "assets/images/omani_halwa.jpg",
      theme_color: "#FFA000",
      bg_style: "linear-gradient(135deg, #FFFDF9 0%, #FFF8EE 100%)",
      active: true,
      order: 1
    },
    {
      id: "banner-heritage-box",
      tag: "🎁 56.29% OFF • GIFT SPECIAL",
      title: "5 Premium Varieties",
      highlight: "Heritage Gift Box",
      subtext: "5 snack-sized snack packs in a royal purple keepsake gift box. The ultimate celebration treat for family & friends.",
      cta_text: "Order Heritage Box (Rs. 699)",
      cta_link: "#heritage-spotlight",
      image: "assets/images/gift_box.jpg",
      theme_color: "#7C3AED",
      bg_style: "linear-gradient(135deg, #FBF8FF 0%, #F5EEFD 100%)",
      active: true,
      order: 2
    },
    {
      id: "banner-fruit-halwa",
      tag: "🌿 100% PURE INGREDIENTS",
      title: "Jackfruit & Tender",
      highlight: "Coconut Halwas",
      subtext: "Fresh seasonal Chakka Varatti & Elaneer halwas crafted with time-tested heritage recipes since 1990.",
      cta_text: "Explore Fruit Halwas",
      cta_link: "#single-varieties",
      image: "assets/images/sooji_halwa.jpg",
      theme_color: "#059669",
      bg_style: "linear-gradient(135deg, #F6FFF9 0%, #EEFBF3 100%)",
      active: true,
      order: 3
    }
  ];

  const state = {
    products: JSON.parse(localStorage.getItem('haweli_products') || JSON.stringify(DEFAULT_PRODUCTS)),
    banners: JSON.parse(localStorage.getItem('haweli_banners') || JSON.stringify(DEFAULT_BANNERS)),
    cart: JSON.parse(localStorage.getItem('haweli_cart') || '[]'),
    activeCategory: 'all',
    appliedCoupon: null,
    discountAmount: 0,
    spotlightQty: 1,
    currentSlide: 0,
    totalSlides: 3,
    slideProgressTimer: null,
    slideDuration: 3600, // 3.6s auto rotate
    progressStepMs: 40,
    progressElapsed: 0
  };

  // Movable Hero Carousel Automatic Engine
  const updateProgressBar = (pct) => {
    const bar = document.getElementById('slider-progress-bar');
    if (bar && state.banners[state.currentSlide]) {
      bar.style.width = `${pct}%`;
      bar.style.backgroundColor = state.banners[state.currentSlide].theme_color || '#FFA000';
    }
  };

  const stopSliderAutoPlay = () => {
    if (state.slideProgressTimer) {
      clearInterval(state.slideProgressTimer);
      state.slideProgressTimer = null;
    }
  };

  const startSliderAutoPlay = () => {
    stopSliderAutoPlay();
    state.progressElapsed = 0;
    updateProgressBar(0);

    const activeCount = (state.banners || []).filter(b => b.active !== false).length;
    if (activeCount <= 1) return;

    state.slideProgressTimer = setInterval(() => {
      state.progressElapsed += state.progressStepMs;
      const pct = Math.min(100, (state.progressElapsed / state.slideDuration) * 100);
      updateProgressBar(pct);

      if (state.progressElapsed >= state.slideDuration) {
        state.progressElapsed = 0;
        nextSlide(true);
      }
    }, state.progressStepMs);
  };

  const goToSlide = (slideIndex, isAutomated = false) => {
    const activeBanners = (state.banners || []).filter(b => b.active !== false);
    state.totalSlides = Math.max(1, activeBanners.length);
    state.currentSlide = (slideIndex + state.totalSlides) % state.totalSlides;
    
    const track = document.getElementById('hero-slider-track');
    if (track) {
      track.style.transform = `translateX(-${state.currentSlide * 100}%)`;
    }

    // Update Dots
    const dots = document.querySelectorAll('.slider-dot');
    dots.forEach((dot, idx) => {
      dot.classList.toggle('active', idx === state.currentSlide);
    });

    if (!isAutomated) {
      startSliderAutoPlay();
    } else {
      updateProgressBar(0);
    }
  };

  const nextSlide = (isAutomated = false) => {
    goToSlide(state.currentSlide + 1, isAutomated);
  };

  const prevSlide = () => {
    goToSlide(state.currentSlide - 1, false);
  };

  const renderHeroSlider = () => {
    const track = document.getElementById('hero-slider-track');
    const dotsContainer = document.getElementById('slider-dots-container');
    if (!track) return;

    const activeBanners = (state.banners || []).filter(b => b.active !== false);
    state.totalSlides = activeBanners.length || 1;

    if (activeBanners.length === 0) {
      track.innerHTML = `
        <div class="hero-slide-item" style="padding: 40px; text-align: center;">
          <h2 class="slide-main-heading">Halwa <span>Haweli</span></h2>
          <p class="slide-sub-text">Authentic Kerala Halwas crafted with heritage recipes since 1990.</p>
        </div>
      `;
      if (dotsContainer) dotsContainer.innerHTML = '';
      return;
    }

    track.innerHTML = activeBanners.map(b => `
      <div class="hero-slide-item" style="background: ${b.bg_style || 'linear-gradient(135deg, #FFFDF9 0%, #FFF8EE 100%)'};">
        <div class="slide-content-left">
          <span class="slide-tag-pill" style="background: ${b.theme_color || '#FFA000'};">${b.tag || '🔥 SPECIAL SALE'}</span>
          <h2 class="slide-main-heading">
            ${b.title} <span style="color: ${b.theme_color || '#FFA000'};">${b.highlight || ''}</span>
          </h2>
          <p class="slide-sub-text">${b.subtext || ''}</p>
          <a href="${b.cta_link || '#single-varieties'}" class="btn-slide-cta" style="background: ${b.theme_color || '#FFA000'};">
            <span>${b.cta_text || 'Order Now'}</span>
            <span>→</span>
          </a>
        </div>
        <div class="slide-image-box">
          <img src="${b.image}" alt="${b.title} ${b.highlight}">
        </div>
      </div>
    `).join('');

    if (dotsContainer) {
      dotsContainer.innerHTML = activeBanners.map((_, idx) => `
        <div class="slider-dot ${idx === state.currentSlide ? 'active' : ''}" onclick="window.haweliApp.goToSlide(${idx})"></div>
      `).join('');
    }

    goToSlide(state.currentSlide, true);
  };

  const formatRs = (num) => 'Rs. ' + Number(num).toFixed(2);

  const saveCart = () => {
    localStorage.setItem('haweli_cart', JSON.stringify(state.cart));
    updateCartUI();
  };

  const showNotification = (msg) => {
    alert(msg);
  };

  const renderProductsGrid = () => {
    const grid = document.getElementById('haweli-products-grid');
    if (!grid) return;

    let items = state.products;
    if (state.activeCategory !== 'all') {
      items = items.filter(p => p.category === state.activeCategory);
    }

    grid.innerHTML = items.map(p => {
      const defaultWeight = (p.weights && p.weights[0]) ? p.weights[0] : { size: "200 g", price: p.base_price, orig: p.original_price };
      const isSoldOut = p.is_sold_out;

      const weightOptionsHtml = (p.weights || []).map(w => `
        <option value="${w.size}" data-price="${w.price}" data-orig="${w.orig || w.orig_price || (w.price * 2)}">
          ${w.size} - Rs. ${w.price}
        </option>
      `).join('');

      return `
        <div class="haweli-card" id="card-${p.id}" data-selected-size="${defaultWeight.size}" data-selected-price="${defaultWeight.price}">
          <div class="card-img-wrap">
            <span class="badge-discount-orange">${p.discount_badge || '50% OFF'}</span>
            <img src="${p.image}" alt="${p.name}">
            <span class="badge-state-pill ${isSoldOut ? 'sold-out' : ''}">${isSoldOut ? 'Sold out' : (p.badge_type || 'Sale')}</span>
          </div>

          <div class="card-details">
            <h3 class="card-title-text">${p.name}</h3>

            <div class="card-rating-row">
              <span class="stars-teal">★★★★★</span>
              <span class="review-count-text">${p.review_count || 5} reviews</span>
            </div>

            <div class="card-price-row">
              <span class="price-strikethrough" id="orig-price-${p.id}">Rs. ${defaultWeight.orig || defaultWeight.orig_price || (defaultWeight.price * 2)}.00</span>
              <span class="price-active-bold" id="active-price-${p.id}">From Rs. ${defaultWeight.price}.00</span>
            </div>

            <div class="card-action-row">
              <select class="weight-select-dropdown" onchange="window.haweliApp.onWeightChange('${p.id}', this)" ${isSoldOut ? 'disabled' : ''}>
                ${weightOptionsHtml}
              </select>

              <button type="button" class="btn-add-cart-orange ${isSoldOut ? 'disabled' : ''}" 
                      onclick="${isSoldOut ? '' : `window.haweliApp.addToCart('${p.id}')`}" 
                      ${isSoldOut ? 'disabled' : ''}>
                ${isSoldOut ? 'Sold out' : 'Add to cart'}
              </button>
            </div>
          </div>
        </div>
      `;
    }).join('');
  };

  const onWeightChange = (prodId, selectEl) => {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const price = parseFloat(selectedOption.getAttribute('data-price'));
    const orig = parseFloat(selectedOption.getAttribute('data-orig'));
    const size = selectedOption.value;

    const card = document.getElementById(`card-${prodId}`);
    if (card) {
      card.setAttribute('data-selected-size', size);
      card.setAttribute('data-selected-price', price);
    }

    const activePriceEl = document.getElementById(`active-price-${prodId}`);
    const origPriceEl = document.getElementById(`orig-price-${prodId}`);
    if (activePriceEl) activePriceEl.textContent = `From Rs. ${price}.00`;
    if (origPriceEl) origPriceEl.textContent = `Rs. ${orig}.00`;
  };

  const addToCart = (prodId) => {
    const product = state.products.find(p => p.id === prodId);
    if (!product) return;

    const card = document.getElementById(`card-${prodId}`);
    const defaultWeight = product.weights ? product.weights[0] : { size: "200 g", price: product.base_price };
    const size = card ? card.getAttribute('data-selected-size') : defaultWeight.size;
    const price = card ? parseFloat(card.getAttribute('data-selected-price')) : defaultWeight.price;

    const cartKey = `${prodId}_${size}`;
    const existing = state.cart.find(i => i.cartKey === cartKey);

    if (existing) {
      existing.quantity += 1;
    } else {
      state.cart.push({
        cartKey,
        id: product.id,
        name: product.name,
        image: product.image,
        size,
        price,
        quantity: 1
      });
    }

    saveCart();
    openCart();
  };

  const adjustSpotlightQty = (delta) => {
    state.spotlightQty = Math.max(1, state.spotlightQty + delta);
    const qtyEl = document.getElementById('spotlight-qty-val');
    if (qtyEl) qtyEl.textContent = state.spotlightQty;
  };

  const addSpotlightBox = () => {
    const cartKey = `heritage-box-500g`;
    const existing = state.cart.find(i => i.cartKey === cartKey);

    if (existing) {
      existing.quantity += state.spotlightQty;
    } else {
      state.cart.push({
        cartKey,
        id: "heritage-box-500g",
        name: "5 Premium Varieties Heritage Gift Box (500 g)",
        image: "assets/images/gift_box.jpg",
        size: "500 g Gift Box",
        price: 699.00,
        quantity: state.spotlightQty
      });
    }

    saveCart();
    openCart();
  };

  const openCart = () => {
    const drawer = document.getElementById('haweli-cart-drawer');
    const overlay = document.getElementById('haweli-cart-overlay');
    if (drawer) drawer.classList.add('open');
    if (overlay) overlay.classList.add('open');
    updateCartUI();
  };

  const closeCart = () => {
    const drawer = document.getElementById('haweli-cart-drawer');
    const overlay = document.getElementById('haweli-cart-overlay');
    if (drawer) drawer.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
  };

  const updateQuantity = (cartKey, delta) => {
    const item = state.cart.find(i => i.cartKey === cartKey);
    if (!item) return;

    item.quantity += delta;
    if (item.quantity <= 0) {
      state.cart = state.cart.filter(i => i.cartKey !== cartKey);
    }
    saveCart();
  };

  const removeItem = (cartKey) => {
    state.cart = state.cart.filter(i => i.cartKey !== cartKey);
    saveCart();
  };

  const updateCartUI = () => {
    const countEl = document.getElementById('header-cart-count');
    const totalItems = state.cart.reduce((sum, i) => sum + i.quantity, 0);

    if (countEl) {
      countEl.textContent = totalItems;
      countEl.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }

    const drawerBody = document.getElementById('cart-drawer-items');
    const subtotalEl = document.getElementById('cart-subtotal-val');
    const finalTotalEl = document.getElementById('cart-final-total-val');
    const discountRow = document.getElementById('cart-discount-row');
    const discountValEl = document.getElementById('cart-discount-val');

    if (!drawerBody) return;

    if (state.cart.length === 0) {
      drawerBody.innerHTML = `
        <div class="cart-empty-state">
          <div style="font-size: 48px; margin-bottom: 12px;">🛍️</div>
          <h3>Your cart is empty</h3>
          <p>Add some authentic Kerala halwa to celebrate sweet moments.</p>
        </div>
      `;
      if (subtotalEl) subtotalEl.textContent = 'Rs. 0.00';
      if (finalTotalEl) finalTotalEl.textContent = 'Rs. 0.00';
      return;
    }

    const subtotal = state.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
    const finalTotal = Math.max(0, subtotal - state.discountAmount);

    if (subtotalEl) subtotalEl.textContent = formatRs(subtotal);
    if (finalTotalEl) finalTotalEl.textContent = formatRs(finalTotal);

    if (discountRow && discountValEl) {
      if (state.discountAmount > 0) {
        discountRow.style.display = 'flex';
        discountValEl.textContent = `- ${formatRs(state.discountAmount)}`;
      } else {
        discountRow.style.display = 'none';
      }
    }

    drawerBody.innerHTML = state.cart.map(item => `
      <div class="cart-drawer-item">
        <img src="${item.image}" alt="${item.name}" class="cart-item-thumb">
        <div class="cart-item-info">
          <div class="cart-item-name">${item.name}</div>
          <div class="cart-item-size">${item.size}</div>
          <div class="cart-item-price">${formatRs(item.price)}</div>
          
          <div class="cart-item-controls">
            <div class="qty-control-haweli">
              <button type="button" class="qty-btn" onclick="window.haweliApp.updateQuantity('${item.cartKey}', -1)">-</button>
              <span class="qty-num">${item.quantity}</span>
              <button type="button" class="qty-btn" onclick="window.haweliApp.updateQuantity('${item.cartKey}', 1)">+</button>
            </div>
            <button type="button" class="btn-remove-item" onclick="window.haweliApp.removeItem('${item.cartKey}')">
              Remove
            </button>
          </div>
        </div>
      </div>
    `).join('');
  };

  const applyDiscount = async () => {
    const input = document.getElementById('coupon-input-field');
    if (!input) return;
    const code = input.value.trim().toUpperCase();

    if (!code) {
      alert('Please enter a valid coupon code.');
      return;
    }

    const subtotal = state.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);

    if (code === 'HAWELI10' || code === 'KERALA10') {
      state.discountAmount = Math.round(subtotal * 0.10);
      state.appliedCoupon = code;
      alert(`🎉 Coupon "${code}" applied successfully! You saved Rs. ${state.discountAmount}.`);
      updateCartUI();
      return;
    }

    try {
      const res = await fetch('backend/api.php?action=validate_coupon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ code, subtotal })
      });
      const data = await res.json();
      if (data.success) {
        state.discountAmount = data.data.discount;
        state.appliedCoupon = code;
        alert(data.message);
        updateCartUI();
      } else {
        alert(data.message || 'Invalid coupon code.');
      }
    } catch (e) {
      alert('Invalid coupon code. Try code "HAWELI10" for 10% off!');
    }
  };

  const openCheckout = () => {
    if (state.cart.length === 0) {
      alert('Your cart is empty.');
      return;
    }
    closeCart();
    const modal = document.getElementById('checkout-modal');
    if (modal) modal.classList.add('open');
  };

  const closeCheckout = () => {
    const modal = document.getElementById('checkout-modal');
    if (modal) modal.classList.remove('open');
  };

  const placeOrder = async (e) => {
    if (e) e.preventDefault();

    const name = document.getElementById('checkout-name')?.value.trim();
    const phone = document.getElementById('checkout-phone')?.value.trim();
    const address = document.getElementById('checkout-address')?.value.trim();
    const payment = document.getElementById('checkout-payment')?.value || 'Cash on Delivery';

    if (!name || !phone || !address) {
      alert('Please fill in your name, phone number, and delivery address.');
      return;
    }

    const subtotal = state.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
    const total = Math.max(0, subtotal - state.discountAmount);
    const orderId = 'HH-' + Math.floor(1000 + Math.random() * 9000);

    const orderRecord = {
      order_id: orderId,
      customer: { name, phone, address },
      items: state.cart.map(i => ({ name: i.name, size: i.size, quantity: i.quantity, price: i.price })),
      subtotal,
      discount: state.discountAmount,
      total,
      payment_method: payment,
      status: 'cooking',
      created_at: new Date().toISOString()
    };

    // Save to server & local storage
    try {
      await fetch('backend/api.php?action=create_order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderRecord)
      });
    } catch (err) {}

    const localOrders = JSON.parse(localStorage.getItem('zaika_local_orders') || '[]');
    localOrders.unshift(orderRecord);
    localStorage.setItem('zaika_local_orders', JSON.stringify(localOrders));

    // Clear cart
    state.cart = [];
    state.discountAmount = 0;
    state.appliedCoupon = null;
    saveCart();

    // Show Confirmation
    const body = document.getElementById('checkout-modal-body');
    if (body) {
      body.innerHTML = `
        <div style="text-align: center; padding: 20px 0;">
          <h3 style="font-size: 20px; font-weight: 800; color: #111827; margin-bottom: 6px;">Order Confirmed!</h3>
          <p style="font-size: 14px; color: #6B7280; margin-bottom: 16px;">
            Thank you, <strong>${name}</strong>. Your authentic halwa is being packaged fresh.
          </p>
          <div style="background: #F9FAFB; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; text-align: left;">
            <div>Order Reference: <strong style="color: #FFA000;">${orderId}</strong></div>
            <div>Total: <strong>Rs. ${total.toFixed(2)}</strong></div>
            <div>Delivery to: ${address}</div>
          </div>
          <a href="track.html?order_id=${orderId}" class="btn-checkout-haweli" style="display: inline-block; width: auto; padding: 10px 24px;">
            Track Live Order Status
          </a>
        </div>
      `;
    }
  };

  const filterCategory = (cat, el) => {
    state.activeCategory = cat;
    document.querySelectorAll('.category-arrow-link').forEach(l => l.classList.remove('active'));
    if (el) el.classList.add('active');
    renderProductsGrid();
  };

  // ==========================================
  // CATALOG & BANNER ADMIN MANAGEMENT METHODS
  // ==========================================
  const getBanners = () => state.banners;

  const saveBanner = async (bannerData) => {
    if (!bannerData.id) {
      bannerData.id = 'banner-' + Date.now();
    }
    const idx = state.banners.findIndex(b => b.id === bannerData.id);
    if (idx >= 0) {
      state.banners[idx] = { ...state.banners[idx], ...bannerData };
    } else {
      state.banners.push(bannerData);
    }

    localStorage.setItem('haweli_banners', JSON.stringify(state.banners));

    try {
      await fetch('backend/api.php?action=save_banner', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(bannerData)
      });
    } catch (e) {}

    renderHeroSlider();
    return bannerData;
  };

  const deleteBanner = async (bannerId) => {
    state.banners = state.banners.filter(b => b.id !== bannerId);
    localStorage.setItem('haweli_banners', JSON.stringify(state.banners));

    try {
      await fetch('backend/api.php?action=delete_banner', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: bannerId })
      });
    } catch (e) {}

    renderHeroSlider();
  };

  const getProducts = () => state.products;

  const saveProduct = async (productData) => {
    if (!productData.id) {
      productData.id = 'halwa-' + (productData.name || 'custom').toLowerCase().replace(/[^a-z0-9]/g, '-') + '-' + Math.floor(100 + Math.random() * 900);
    }
    const idx = state.products.findIndex(p => p.id === productData.id);
    if (idx >= 0) {
      state.products[idx] = { ...state.products[idx], ...productData };
    } else {
      state.products.push(productData);
    }

    localStorage.setItem('haweli_products', JSON.stringify(state.products));

    try {
      await fetch('backend/api.php?action=save_product', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(productData)
      });
    } catch (e) {}

    renderProductsGrid();
    return productData;
  };

  const updateProductPrices = async (id, basePrice, originalPrice, discountBadge, weights) => {
    const prod = state.products.find(p => p.id === id);
    if (prod) {
      if (basePrice) prod.base_price = parseFloat(basePrice);
      if (originalPrice) prod.original_price = parseFloat(originalPrice);
      if (discountBadge) prod.discount_badge = discountBadge;
      if (weights) prod.weights = weights;

      localStorage.setItem('haweli_products', JSON.stringify(state.products));

      try {
        await fetch('backend/api.php?action=update_product_prices', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, base_price: basePrice, original_price: originalPrice, discount_badge: discountBadge, weights })
        });
      } catch (e) {}

      renderProductsGrid();
    }
  };

  const deleteProduct = async (prodId) => {
    state.products = state.products.filter(p => p.id !== prodId);
    localStorage.setItem('haweli_products', JSON.stringify(state.products));

    try {
      await fetch('backend/api.php?action=delete_product', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: prodId })
      });
    } catch (e) {}

    renderProductsGrid();
  };

  const setupSliderInteractions = () => {
    const sliderWrapper = document.getElementById('hero-slider-wrapper');
    if (!sliderWrapper) return;

    startSliderAutoPlay();

    sliderWrapper.addEventListener('mouseenter', stopSliderAutoPlay);
    sliderWrapper.addEventListener('mouseleave', startSliderAutoPlay);

    let touchStartX = 0;
    let touchEndX = 0;

    sliderWrapper.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
      stopSliderAutoPlay();
    }, { passive: true });

    sliderWrapper.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      const diffX = touchStartX - touchEndX;
      if (diffX > 45) {
        nextSlide(false);
      } else if (diffX < -45) {
        prevSlide();
      }
      startSliderAutoPlay();
    }, { passive: true });
  };

  // Asynchronously fetch latest data from backend if available
  const syncWithServer = async () => {
    try {
      const [bannersRes, prodRes] = await Promise.all([
        fetch('backend/api.php?action=get_banners'),
        fetch('backend/api.php?action=get_products')
      ]);
      const bannersData = await bannersRes.json();
      const prodData = await prodRes.json();

      if (bannersData.success && Array.isArray(bannersData.data) && bannersData.data.length > 0) {
        state.banners = bannersData.data;
        localStorage.setItem('haweli_banners', JSON.stringify(bannersData.data));
        renderHeroSlider();
      }

      if (prodData.success && Array.isArray(prodData.data) && prodData.data.length > 0) {
        state.products = prodData.data;
        localStorage.setItem('haweli_products', JSON.stringify(prodData.data));
        renderProductsGrid();
      }
    } catch (e) {}
  };

  const initApp = () => {
    renderHeroSlider();
    renderProductsGrid();
    updateCartUI();
    setupSliderInteractions();
    syncWithServer();
  };

  window.haweliApp = {
    goToSlide,
    nextSlide,
    prevSlide,
    renderHeroSlider,
    renderProductsGrid,
    onWeightChange,
    addToCart,
    adjustSpotlightQty,
    addSpotlightBox,
    openCart,
    closeCart,
    updateQuantity,
    removeItem,
    applyDiscount,
    openCheckout,
    closeCheckout,
    placeOrder,
    filterCategory,
    // Admin features
    getBanners,
    saveBanner,
    deleteBanner,
    getProducts,
    saveProduct,
    updateProductPrices,
    deleteProduct
  };

  // Backwards compatibility for tracker
  window.halwaApp = {
    performTrackLookup: async (query) => {
      const container = document.getElementById('live-tracker-view');
      if (!container) return;

      const local = JSON.parse(localStorage.getItem('zaika_local_orders') || '[]');
      const order = local.find(o => o.order_id.toLowerCase() === query.toLowerCase()) || {
        order_id: query || "HH-8842",
        customer: { name: "Hamza Farooqi", phone: "+91 9876543210", address: "Kozhikode, Kerala" },
        items: [{ name: "Dates Halwa - Authentic, Kerala Halwa", size: "200 g", quantity: 2, price: 299 }],
        total: 598.00,
        status: "cooking",
        delivery_slot: "Dispatched Fresh"
      };

      container.innerHTML = `
        <div style="background:#FFF; border:1px solid #E5E7EB; border-radius:12px; padding:24px;">
          <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #E5E7EB; padding-bottom:12px; margin-bottom:16px;">
            <div>
              <span style="font-size:12px; color:#6B7280; text-transform:uppercase;">Order ID</span>
              <h2 style="font-size:20px; font-weight:800; color:#111827; margin:0;">${order.order_id}</h2>
            </div>
            <span style="background:#FFFBEB; color:#B45309; font-weight:700; padding:4px 12px; border-radius:20px; font-size:13px;">
              ${order.status === 'cooking' ? 'Preparing Fresh' : 'Out for Delivery'}
            </span>
          </div>
          <div>
            ${order.items.map(i => `
              <div style="display:flex; justify-content:space-between; padding:6px 0; font-size:14px; border-bottom:1px solid #F3F4F6;">
                <span>${i.quantity}x ${i.name} (${i.size})</span>
                <strong>Rs. ${(i.price * i.quantity).toFixed(2)}</strong>
              </div>
            `).join('')}
            <div style="display:flex; justify-content:space-between; font-size:16px; font-weight:800; margin-top:12px;">
              <span>Total:</span>
              <span>Rs. ${parseFloat(order.total).toFixed(2)}</span>
            </div>
          </div>
        </div>
      `;
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
  } else {
    initApp();
  }

  window.addEventListener('load', () => {
    if (!state.slideProgressTimer) {
      setupSliderInteractions();
    }
  });
})();
