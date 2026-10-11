(function () {
  'use strict';

  var menuBtn = document.querySelector('.sg-header__menu-btn');
  var mobilePanel = document.getElementById('sg-mobile-panel');
  var mobileBar = document.getElementById('sg-mobile-bar');
  var footer = document.getElementById('sg-footer');
  var reveals = document.querySelectorAll('.sg-reveal');

  if (menuBtn && mobilePanel) {
    menuBtn.addEventListener('click', function () {
      var expanded = menuBtn.getAttribute('aria-expanded') === 'true';
      menuBtn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      mobilePanel.hidden = expanded;
    });
  }

  var dropdownItems = document.querySelectorAll('.sg-header__nav-item--dropdown');
  var dropdownCloseDelayMs = 180;
  var dropdownOpenDelayMs = 70;

  dropdownItems.forEach(function (item) {
    var menu = item.querySelector('.sg-header__nav-dropdown-wrap > .sg-header__nav-menu');
    if (!menu) {
      return;
    }

    var trigger = item.querySelector('.sg-header__nav-link--parent');
    var closeTimer = null;
    var openTimer = null;

    function setExpanded(expanded) {
      if (trigger) {
        trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      }
    }

    function closeItem(target) {
      target.classList.remove('is-open');
      var otherTrigger = target.querySelector('.sg-header__nav-link--parent');
      if (otherTrigger) {
        otherTrigger.setAttribute('aria-expanded', 'false');
      }
    }

    function openMenu() {
      if (openTimer) {
        clearTimeout(openTimer);
        openTimer = null;
      }
      if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
      }

      dropdownItems.forEach(function (other) {
        if (other !== item) {
          closeItem(other);
        }
      });

      item.classList.add('is-open');
      setExpanded(true);
    }

    function scheduleOpen() {
      if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
      }
      if (item.classList.contains('is-open')) {
        openMenu();
        return;
      }
      if (openTimer) {
        clearTimeout(openTimer);
      }
      openTimer = setTimeout(openMenu, dropdownOpenDelayMs);
    }

    function scheduleClose() {
      if (openTimer) {
        clearTimeout(openTimer);
        openTimer = null;
      }
      if (closeTimer) {
        clearTimeout(closeTimer);
      }
      closeTimer = setTimeout(function () {
        closeItem(item);
        closeTimer = null;
      }, dropdownCloseDelayMs);
    }

    item.addEventListener('mouseenter', scheduleOpen);
    item.addEventListener('mouseleave', scheduleClose);
    item.addEventListener('focusin', openMenu);
    item.addEventListener('focusout', function (event) {
      if (!item.contains(event.relatedTarget)) {
        scheduleClose();
      }
    });
  });

  var previewMenus = document.querySelectorAll('.sg-header__nav-menu--preview');
  previewMenus.forEach(function (menu) {
    var img = menu.querySelector('.sg-header__mega-preview-img');
    var placeholder = menu.querySelector('.sg-header__mega-preview-placeholder');
    var caption = menu.querySelector('.sg-header__mega-preview-caption');
    var links = menu.querySelectorAll('.sg-header__mega-preview-link');

    function activatePreview(link) {
      links.forEach(function (item) {
        item.classList.remove('is-active');
      });
      link.classList.add('is-active');

      var src = link.getAttribute('data-preview-src') || '';
      var alt = link.getAttribute('data-preview-alt') || '';

      if (caption) {
        caption.textContent = alt;
      }

      if (img && src) {
        img.src = src;
        img.alt = alt;
        img.hidden = false;
        if (placeholder) {
          placeholder.hidden = true;
        }
        return;
      }

      if (img) {
        img.hidden = true;
      }
      if (placeholder) {
        placeholder.hidden = false;
      }
    }

    links.forEach(function (link) {
      link.addEventListener('mouseenter', function () {
        activatePreview(link);
      });
      link.addEventListener('focus', function () {
        activatePreview(link);
      });
    });
  });

  var featureMenus = document.querySelectorAll('.sg-header__nav-menu--feature');
  featureMenus.forEach(function (menu) {
    var img = menu.querySelector('.sg-header__mega-feature-img');
    var placeholder = menu.querySelector('.sg-header__mega-feature-placeholder');
    var title = menu.querySelector('.sg-header__mega-feature-title');
    var titleText = menu.querySelector('.sg-header__mega-feature-title-text');
    var blurb = menu.querySelector('.sg-header__mega-feature-blurb');
    var links = menu.querySelectorAll('.sg-header__mega-feature-link');

    function activateFeature(link) {
      links.forEach(function (item) {
        item.classList.remove('is-active');
      });
      link.classList.add('is-active');

      var src = link.getAttribute('data-preview-src') || '';
      var alt = link.getAttribute('data-preview-alt') || '';
      var desc = link.getAttribute('data-preview-blurb') || '';
      var href = link.getAttribute('href') || '';

      if (title && href) {
        title.setAttribute('href', href);
      }
      if (titleText) {
        titleText.textContent = alt;
      }
      if (blurb) {
        blurb.textContent = desc;
      }

      if (img && src) {
        img.src = src;
        img.hidden = false;
        if (placeholder) {
          placeholder.hidden = true;
        }
        return;
      }

      if (img) {
        img.hidden = true;
      }
      if (placeholder) {
        placeholder.hidden = false;
      }
    }

    links.forEach(function (link) {
      link.addEventListener('mouseenter', function () {
        activateFeature(link);
      });
      link.addEventListener('focus', function () {
        activateFeature(link);
      });
    });
  });

  if ('IntersectionObserver' in window && reveals.length) {
    var revealObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            revealObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.08, rootMargin: '0px 0px -24px 0px' }
    );

    reveals.forEach(function (el) {
      revealObserver.observe(el);
    });
  } else {
    reveals.forEach(function (el) {
      el.classList.add('is-visible');
    });
  }

  function updateMobileBar() {
    if (!mobileBar || window.innerWidth >= 768) {
      mobileBar.classList.remove('is-visible');
      mobileBar.setAttribute('aria-hidden', 'true');
      return;
    }

    var hide = false;
    if (footer) {
      var rect = footer.getBoundingClientRect();
      if (rect.top < window.innerHeight && rect.bottom > 0) {
        hide = true;
      }
    }

    if (hide) {
      mobileBar.classList.remove('is-visible');
      mobileBar.setAttribute('aria-hidden', 'true');
    } else {
      mobileBar.classList.add('is-visible');
      mobileBar.setAttribute('aria-hidden', 'false');
    }
  }

  window.addEventListener('scroll', updateMobileBar, { passive: true });
  window.addEventListener('resize', updateMobileBar);
  updateMobileBar();

  var faqTriggers = document.querySelectorAll('.sg-value-faq__trigger');
  faqTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var item = trigger.closest('.sg-value-faq__item');
      var panel = item ? item.querySelector('.sg-value-faq__panel') : null;
      if (!item || !panel) {
        return;
      }

      var isOpen = trigger.getAttribute('aria-expanded') === 'true';
      trigger.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      item.classList.toggle('is-open', !isOpen);
      panel.hidden = isOpen;
    });
  });
})();
