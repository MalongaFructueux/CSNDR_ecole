/* CSNDR Enhanced App.js - Modern School Management System */
(function(){
  'use strict';
  
  function ready(fn){ if(document.readyState!=='loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }

  // Enhanced Toast System with Modern Design
  function ensureToastRoot(){
    let root = document.getElementById('toast-root');
    if (!root){
      root = document.createElement('div');
      root.id = 'toast-root';
      root.style.cssText = `
        position: fixed;
        top: 1.5rem;
        right: 1.5rem;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        pointer-events: none;
        max-width: 400px;
      `;
      document.body.appendChild(root);
    }
    return root;
  }

  function toast(message, type = 'success'){
    const root = ensureToastRoot();
    const el = document.createElement('div');
    
    // Modern toast styling with CSNDR color palette
    const styles = {
      success: 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white border-emerald-400',
      error: 'bg-gradient-to-r from-red-500 to-red-600 text-white border-red-400',
      warning: 'bg-gradient-to-r from-amber-500 to-amber-600 text-white border-amber-400',
      info: 'bg-gradient-to-r from-blue-500 to-blue-600 text-white border-blue-400'
    };
    
    const icons = {
      success: '✓',
      error: '✕',
      warning: '⚠',
      info: 'ℹ'
    };
    
    el.innerHTML = `
      <div class="flex items-center gap-3">
        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold">
          ${icons[type] || icons.success}
        </div>
        <div class="flex-1 text-sm font-medium">${message}</div>
        <button class="flex-shrink-0 w-5 h-5 rounded-full hover:bg-white/20 flex items-center justify-center text-xs" onclick="this.parentElement.parentElement.remove()">
          ✕
        </button>
      </div>
    `;
    
    el.className = `${styles[type] || styles.success} px-4 py-3 rounded-2xl shadow-lg border backdrop-blur-sm pointer-events-auto transform transition-all duration-300 ease-out`;
    el.style.cssText = 'opacity: 0; transform: translateX(100%) scale(0.95);';
    
    root.appendChild(el);
    
    // Animate in
    requestAnimationFrame(() => {
      el.style.cssText = 'opacity: 1; transform: translateX(0) scale(1);';
    });
    
    // Auto remove after 4 seconds
    setTimeout(() => {
      el.style.cssText = 'opacity: 0; transform: translateX(100%) scale(0.95);';
      setTimeout(() => el.remove(), 300);
    }, 4000);
  }
  
  window.CSNDRToast = toast;

  // Enhanced Button Ripple Effect
  function addRipple(el){
    el.style.overflow = 'hidden';
    el.style.position = 'relative';
    
    el.addEventListener('click', function(e){
      const rect = el.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const ripple = document.createElement('span');
      
      ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
        width: ${size}px;
        height: ${size}px;
        left: ${e.clientX - rect.left - size/2}px;
        top: ${e.clientY - rect.top - size/2}px;
        background: rgba(255, 255, 255, 0.4);
        transform: scale(0);
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 0.8s ease;
        z-index: 0;
      `;
      
      el.appendChild(ripple);
      
      requestAnimationFrame(() => {
        ripple.style.transform = 'scale(2)';
        ripple.style.opacity = '0';
      });
      
      setTimeout(() => ripple.remove(), 800);
    });
  }

  // Enhanced Button System
  function enhanceButtons(){
    document.querySelectorAll('.btn, .btn-primary, .btn-secondary, .btn-success, .btn-danger, .btn-ghost').forEach(btn => {
      if (!btn.dataset.enhanced) {
        btn.dataset.enhanced = '1';
        addRipple(btn);
        
        // Add loading state capability
        btn.addEventListener('click', function(e) {
          if (this.dataset.loading === 'true') {
            e.preventDefault();
            return false;
          }
        });
      }
    });
  }

  // Loading State for Buttons
  window.setButtonLoading = function(button, loading = true) {
    if (typeof button === 'string') {
      button = document.querySelector(button);
    }
    if (!button) return;
    
    if (loading) {
      button.dataset.loading = 'true';
      button.dataset.originalText = button.innerHTML;
      button.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin mr-2"></i>Chargement...';
      button.disabled = true;
      button.classList.add('loading');
    } else {
      button.dataset.loading = 'false';
      button.innerHTML = button.dataset.originalText || button.innerHTML;
      button.disabled = false;
      button.classList.remove('loading');
    }
    
    // Refresh lucide icons
    if (window.lucide) {
      window.lucide.createIcons();
    }
  };

  // Modern Animation System
  function animateIn(el, delay = 0){
    const mode = el.getAttribute('data-animate') || 'fade';
    
    setTimeout(() => {
      el.style.willChange = 'opacity, transform';
      el.style.transition = 'opacity 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94), transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
      
      switch(mode) {
        case 'slide':
        case 'slide-up':
          el.style.opacity = '0';
          el.style.transform = 'translateY(20px)';
          requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
          });
          break;
        case 'slide-down':
          el.style.opacity = '0';
          el.style.transform = 'translateY(-20px)';
          requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
          });
          break;
        case 'scale':
        case 'scale-in':
          el.style.opacity = '0';
          el.style.transform = 'scale(0.95)';
          requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'scale(1)';
          });
          break;
        default: // fade
          el.style.opacity = '0';
          requestAnimationFrame(() => {
            el.style.opacity = '1';
          });
      }
    }, delay);
  }

  // Enhanced Tab System with Persistence
  function initTabs(){
    document.querySelectorAll('[data-tab]').forEach(btn => {
      const scope = btn.closest('[data-tab-scope]');
      if (!scope) return;
      
      const key = 'csndr_tab_' + (scope.getAttribute('data-tab-scope') || 'default');
      const panels = scope.querySelectorAll('[data-panel]');
      const btns = scope.querySelectorAll('[data-tab]');
      
      function activate(tab) {
        btns.forEach(b => b.classList.toggle('tab-active', b.getAttribute('data-tab') === tab));
        panels.forEach(p => {
          const show = p.getAttribute('data-panel') === tab;
          p.classList.toggle('hidden', !show);
          if (show) animateIn(p);
        });
        try { localStorage.setItem(key, tab); } catch(e) {}
      }
      
      const saved = localStorage.getItem(key);
      const defaultTab = (btns[0] || {}).getAttribute?.('data-tab') || 'profil';
      const initial = saved || defaultTab;
      
      if (!scope.dataset.tabsInit) {
        activate(initial);
        scope.dataset.tabsInit = '1';
      }
      
      btns.forEach(b => b.addEventListener('click', () => activate(b.getAttribute('data-tab'))));
    });
  }

  // Enhanced Form Validation
  function enhanceFormValidation() {
    document.querySelectorAll('form').forEach(form => {
      const inputs = form.querySelectorAll('input, textarea, select');
      
      inputs.forEach(input => {
        input.addEventListener('blur', function() {
          validateField(this);
        });
        
        input.addEventListener('input', function() {
          if (this.classList.contains('error')) {
            validateField(this);
          }
        });
      });
      
      form.addEventListener('submit', function(e) {
        let isValid = true;
        inputs.forEach(input => {
          if (!validateField(input)) {
            isValid = false;
          }
        });
        
        if (!isValid) {
          e.preventDefault();
          CSNDRToast('Veuillez corriger les erreurs dans le formulaire', 'error');
        }
      });
    });
  }

  function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
      isValid = false;
      message = 'Ce champ est requis';
    }
    
    // Email validation
    if (field.type === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
      isValid = false;
      message = 'Adresse email invalide';
    }
    
    // Password validation
    if (field.type === 'password' && value && value.length < 6) {
      isValid = false;
      message = 'Le mot de passe doit contenir au moins 6 caractères';
    }
    
    // Update field appearance
    field.classList.toggle('error', !isValid);
    
    // Show/hide error message
    let errorEl = field.parentNode.querySelector('.field-error');
    if (!isValid && message) {
      if (!errorEl) {
        errorEl = document.createElement('div');
        errorEl.className = 'field-error text-xs text-red-600 mt-1';
        field.parentNode.appendChild(errorEl);
      }
      errorEl.textContent = message;
    } else if (errorEl) {
      errorEl.remove();
    }
    
    return isValid;
  }

  // Smooth Page Transitions
  function initPageTransitions() {
    document.querySelectorAll('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"])').forEach(link => {
      link.addEventListener('click', function(e) {
        // Allow modified clicks
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
        
        e.preventDefault();
        const href = this.getAttribute('href');
        
        // Add loading state
        document.body.style.opacity = '0.7';
        document.body.style.transition = 'opacity 0.2s ease';
        
        setTimeout(() => {
          window.location.href = href;
        }, 200);
      });
    });
  }

  // Staggered Animations
  function initStaggeredAnimations() {
    document.querySelectorAll('[data-stagger]').forEach(container => {
      const selector = container.getAttribute('data-stagger') || ':scope > *';
      const children = container.querySelectorAll(selector);
      
      children.forEach((child, index) => {
        child.style.opacity = '0';
        child.style.transform = 'translateY(20px)';
        child.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        
        setTimeout(() => {
          child.style.opacity = '1';
          child.style.transform = 'translateY(0)';
        }, index * 100);
      });
    });
  }

  // Main Initialization Function
  ready(function(){
    // Smooth page entrance
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.3s ease';
    requestAnimationFrame(() => {
      document.body.style.opacity = '1';
    });

    // Initialize all enhancements
    enhanceButtons();
    initTabs();
    enhanceFormValidation();
    initPageTransitions();
    initStaggeredAnimations();
    
    // Initialize animations
    document.querySelectorAll('[data-animate]').forEach((el, index) => {
      animateIn(el, index * 50);
    });

    // Enhanced input interactions
    document.querySelectorAll('input, textarea, select').forEach(el => {
      el.addEventListener('focus', function() {
        this.style.transform = 'scale(1.02)';
        this.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
      });
      
      el.addEventListener('blur', function() {
        this.style.transform = 'scale(1)';
      });
      
      el.addEventListener('invalid', function() {
        this.animate([
          { transform: 'translateX(0)' },
          { transform: 'translateX(-5px)' },
          { transform: 'translateX(5px)' },
          { transform: 'translateX(0)' }
        ], {
          duration: 300,
          easing: 'ease-in-out'
        });
      });
    });

    // Enhanced confirmation dialogs
    document.addEventListener('click', (e) => {
      const confirmEl = e.target.closest('[data-confirm]');
      if (confirmEl) {
        const message = confirmEl.getAttribute('data-confirm');
        if (!confirm(`⚠️ ${message}\n\nCette action est irréversible.`)) {
          e.preventDefault();
        }
      }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
      // Escape key to close modals/dropdowns
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal, .dropdown.open').forEach(el => {
          el.classList.remove('open', 'show');
        });
      }
    });

    // Auto-save form data (for better UX)
    document.querySelectorAll('form[data-autosave]').forEach(form => {
      const formId = form.getAttribute('data-autosave');
      const inputs = form.querySelectorAll('input, textarea, select');
      
      // Load saved data
      inputs.forEach(input => {
        const key = `csndr_form_${formId}_${input.name}`;
        const saved = localStorage.getItem(key);
        if (saved && !input.value) {
          input.value = saved;
        }
      });
      
      // Save data on change
      inputs.forEach(input => {
        input.addEventListener('input', () => {
          const key = `csndr_form_${formId}_${input.name}`;
          localStorage.setItem(key, input.value);
        });
      });
      
      // Clear saved data on successful submit
      form.addEventListener('submit', () => {
        inputs.forEach(input => {
          const key = `csndr_form_${formId}_${input.name}`;
          localStorage.removeItem(key);
        });
      });
    });
  });

})();
      function activate(tab){
        btns.forEach(b=>b.classList.toggle('tab-active', b.getAttribute('data-tab')===tab));
        panels.forEach(p=>{
          const show = p.getAttribute('data-panel')===tab;
          p.classList.toggle('hidden', !show);
          if (show) animateIn(p);
        });
        try{ localStorage.setItem(key, tab); }catch{}
      }
      const saved = localStorage.getItem(key);
      const defaultTab = (btns[0]||{}).getAttribute?.('data-tab') || 'profil';
      const initial = saved || defaultTab;
      if (!scope.dataset.tabsInit){ activate(initial); scope.dataset.tabsInit = '1'; }
      btns.forEach(b=> b.addEventListener('click', ()=>activate(b.getAttribute('data-tab'))));
    });
  }

  ready(function(){
    // Page fade-in
    try{ document.body.style.opacity = '0'; document.body.style.transition = 'opacity 180ms ease'; requestAnimationFrame(()=>{ document.body.style.opacity = '1'; }); }catch{}

    enhanceButtons();
    initTabs();
    document.querySelectorAll('[data-animate]').forEach(animateIn);

    // Page transitions for links
    document.querySelectorAll('a[href]:not([target="_blank"])').forEach(a=>{
      const href = a.getAttribute('href')||'';
      if (href.startsWith('#') || href.startsWith('javascript:')) return;
      a.addEventListener('click', (e)=>{
        // Allow modified clicks
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button!==0) return;
        e.preventDefault();
        try{ document.body.style.opacity = '0'; }catch{}
        setTimeout(()=>{ window.location.href = href; }, 150);
      });
    });

    // Inputs micro-interactions
    document.querySelectorAll('input, textarea, select').forEach(el=>{
      el.addEventListener('focus', ()=>{ el.style.boxShadow = '0 0 0 3px rgba(59,130,246,.35)'; });
      el.addEventListener('blur', ()=>{ el.style.boxShadow = ''; });
      el.addEventListener('invalid', ()=>{
        if (el.animate){ el.animate([{transform:'translateX(0)'},{transform:'translateX(-4px)'},{transform:'translateX(4px)'},{transform:'translateX(0)'}], {duration:250}); }
      });
    });

    // Stagger in lists/tables
    document.querySelectorAll('[data-stagger]').forEach(container=>{
      const children = container.querySelectorAll(container.getAttribute('data-stagger')||':scope > *');
      children.forEach((c,i)=>{
        c.style.opacity = '0'; c.style.transform = 'translateY(6px)'; c.style.transition = 'opacity 240ms ease, transform 240ms ease';
        setTimeout(()=>{ c.style.opacity = '1'; c.style.transform = 'translateY(0)'; }, 40*i);
      });
    });
  });
})();
