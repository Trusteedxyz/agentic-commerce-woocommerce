/**
 * Trusteed for WooCommerce — Settings page scripts.
 *
 * Handles: Onboard, Disconnect, Test Connection, Sync Catalog AJAX actions.
 *
 * @package Trusteed
 * @since   1.0.0
 */

(function ($) {
  "use strict";

  var cfg = agenticmcp_settings;
  var i18n = cfg.i18n;

  // ── Onboard form ───────────────────────────────────────────────────────────

  $("#amcp-onboard-btn").on("click", function (e) {
    e.preventDefault();

    var $btn = $(this);
    var $result = $("#amcp-onboard-result");
    var email = $.trim($("#amcp-onboard-email").val());
    var pwd = $("#amcp-onboard-password").val();

    if (!email || !pwd) {
      $result.html(
        '<span class="agenticmcp-badge agenticmcp-badge--error">' +
          "Correo y contraseña son obligatorios." +
          "</span>"
      );
      return;
    }

    $btn.prop("disabled", true).text(i18n.connecting);
    $result.html('<span class="spinner is-active"></span>');

    $.post(
      cfg.ajax_url,
      {
        action: "agenticmcp_onboard",
        _ajax_nonce: cfg.onboard_nonce,
        email: email,
        password: pwd,
      },
      function (response) {
        if (response.success) {
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--success">' +
              $("<span>").text(response.data.message).html() +
              "</span>"
          );
          // Reload page so the connected state is shown
          setTimeout(function () {
            window.location.reload();
          }, 1200);
        } else {
          var msg =
            response.data && response.data.message
              ? response.data.message
              : i18n.request_failed;
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--error">' +
              $("<span>").text(msg).html() +
              "</span>"
          );
          $btn.prop("disabled", false).text(i18n.connect_btn);
        }
      }
    ).fail(function () {
      $result.html(
        '<span class="agenticmcp-badge agenticmcp-badge--error">' +
          i18n.request_failed +
          "</span>"
      );
      $btn.prop("disabled", false).text(i18n.connect_btn);
    });
  });

  // ── Connect with API key (no password) ──────────────────────────────────────

  $("#amcp-connect-key-btn").on("click", function (e) {
    e.preventDefault();

    var $btn = $(this);
    var $result = $("#amcp-connect-key-result");
    var key = $.trim($("#amcp-connect-key").val());
    var label = $btn.text();

    if (!key) {
      $result.html(
        '<span class="agenticmcp-badge agenticmcp-badge--error">' +
          "Introduce tu API key." +
          "</span>"
      );
      return;
    }

    $btn.prop("disabled", true).text(i18n.connecting);
    $result.html('<span class="spinner is-active"></span>');

    $.post(
      cfg.ajax_url,
      {
        action: "agenticmcp_connect_key",
        _ajax_nonce: cfg.connect_key_nonce,
        api_key: key,
      },
      function (response) {
        if (response.success) {
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--success">' +
              $("<span>").text(response.data.message).html() +
              "</span>"
          );
          setTimeout(function () {
            window.location.reload();
          }, 1200);
        } else {
          var msg =
            response.data && response.data.message
              ? response.data.message
              : i18n.request_failed;
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--error">' +
              $("<span>").text(msg).html() +
              "</span>"
          );
          $btn.prop("disabled", false).text(label);
        }
      }
    ).fail(function () {
      $result.html(
        '<span class="agenticmcp-badge agenticmcp-badge--error">' +
          i18n.request_failed +
          "</span>"
      );
      $btn.prop("disabled", false).text(label);
    });
  });

  // ── Disconnect ─────────────────────────────────────────────────────────────

  $("#amcp-disconnect-btn").on("click", function (e) {
    e.preventDefault();

    if (!window.confirm(i18n.disconnect_confirm)) {
      return;
    }

    var $btn = $(this);
    var $result = $("#amcp-disconnect-result");

    $btn.prop("disabled", true).text(i18n.disconnecting);

    $.post(
      cfg.ajax_url,
      {
        action: "agenticmcp_disconnect",
        _ajax_nonce: cfg.disconnect_nonce,
      },
      function (response) {
        if (response.success) {
          window.location.reload();
        } else {
          var msg =
            response.data && response.data.message
              ? response.data.message
              : i18n.request_failed;
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--error">' +
              $("<span>").text(msg).html() +
              "</span>"
          );
          $btn.prop("disabled", false).text("Desconectar tienda");
        }
      }
    ).fail(function () {
      $result.html(
        '<span class="agenticmcp-badge agenticmcp-badge--error">' +
          i18n.request_failed +
          "</span>"
      );
      $btn.prop("disabled", false).text("Desconectar tienda");
    });
  });

  // ── Test Connection ────────────────────────────────────────────────────────

  $("#agenticmcp-test-connection").on("click", function (e) {
    e.preventDefault();

    var $btn = $(this);
    var $result = $("#agenticmcp-connection-result");

    $btn.prop("disabled", true).text(i18n.testing);
    $result.html("");

    $.post(
      cfg.ajax_url,
      { action: "agenticmcp_test_connection", _ajax_nonce: cfg.test_nonce },
      function (response) {
        if (response.success) {
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--success">' +
              i18n.connected +
              " (" +
              response.data.latency_ms +
              "ms)" +
              "</span>"
          );
        } else {
          var msg =
            response.data && response.data.message
              ? response.data.message
              : i18n.request_failed;
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--error">' +
              i18n.failed +
              ": " +
              $("<span>").text(msg).html() +
              "</span>"
          );
        }
      }
    )
      .fail(function () {
        $result.html(
          '<span class="agenticmcp-badge agenticmcp-badge--error">' +
            i18n.request_failed +
            "</span>"
        );
      })
      .always(function () {
        $btn.prop("disabled", false).text(i18n.test_btn);
      });
  });

  // ── Sync Catalog ───────────────────────────────────────────────────────────

  $("#agenticmcp-sync-catalog").on("click", function (e) {
    e.preventDefault();

    var $btn = $(this);
    var $result = $("#agenticmcp-sync-result");

    $btn.prop("disabled", true).text(i18n.syncing);
    $result.html('<span class="spinner is-active"></span>');

    $.post(
      cfg.ajax_url,
      { action: "agenticmcp_sync_catalog", _ajax_nonce: cfg.sync_nonce },
      function (response) {
        if (response.success) {
          var msg =
            response.data && response.data.message
              ? response.data.message
              : "Sincronización completada.";
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--success">' +
              $("<span>").text(msg).html() +
              "</span>"
          );
        } else {
          var errMsg =
            response.data && response.data.message
              ? response.data.message
              : i18n.request_failed;
          $result.html(
            '<span class="agenticmcp-badge agenticmcp-badge--error">' +
              i18n.failed +
              ": " +
              $("<span>").text(errMsg).html() +
              "</span>"
          );
        }
      }
    )
      .fail(function () {
        $result.html(
          '<span class="agenticmcp-badge agenticmcp-badge--error">' +
            i18n.request_failed +
            "</span>"
        );
      })
      .always(function () {
        $btn.prop("disabled", false).text(i18n.sync_btn);
      });
  });
})(jQuery);
