'use strict';

/**
 * Mounts the SmartyParcel help assistant. The assistant key lives on the server: connect and token go through
 * admin-ajax, the browser gets only short-lived tokens.
 */
(function () {
  const config = window.wcus_assistant;
  if (!config || typeof window.SmartyParcelAssistant === 'undefined') {
    return;
  }

  let hasConsent = !!config.hasConsent;

  const post = async (action) => {
    const body = new URLSearchParams({action: action, _token: config.nonce});
    const response = await fetch(config.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      body: body,
    });
    const result = await response.json();
    if (!result || !result.success) {
      throw new Error((result && result.error && result.error.message) || 'Assistant request failed: ' + action);
    }

    return result.data;
  };

  window.SmartyParcelAssistant.mount({
    apiUrl: config.apiUrl,
    hasConsent: async () => hasConsent,
    connect: async () => {
      await post('wcus_assistant_connect');
      hasConsent = true;
    },
    getToken: async () => {
      const data = await post('wcus_assistant_token');

      return {token: data.token, expiresAt: data.expires_at};
    },
    getContext: async () => config.context || {},
    consent: config.consent,
    locale: config.locale,
    title: config.title,
  });
})();
