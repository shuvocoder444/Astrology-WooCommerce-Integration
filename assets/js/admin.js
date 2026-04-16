/**
 * Admin JS - Dynamic Form Type Mapping
 */
(function($) {
    'use strict';

    var mappings   = astroWooAdmin.mappings   || [];
    var fieldTypes = astroWooAdmin.fieldTypes || {};
    var ajaxUrl    = astroWooAdmin.ajaxUrl;
    var nonce      = astroWooAdmin.nonce;

    /* =========================================================
       UTILITY
    ========================================================= */
    function showNotice(msg, type) {
        type = type || 'success';
        var cls = type === 'error' ? 'notice-error' : 'notice-success';
        $('#astro-mapping-notice').html(
            '<div class="notice ' + cls + ' is-dismissible"><p>' + msg + '</p></div>'
        );
        setTimeout(function(){ $('#astro-mapping-notice .notice').fadeOut(); }, 4000);
    }

    function fieldTypeOptions(selected) {
        var html = '';
        $.each(fieldTypes, function(val, label) {
            html += '<option value="' + val + '"' + (val === selected ? ' selected' : '') + '>' + label + '</option>';
        });
        return html;
    }

    /* =========================================================
       REBUILD TABLE
    ========================================================= */
    function rebuildTable(newMappings) {
        mappings = newMappings || mappings;
        var tbody = $('#astro-mapping-tbody');
        tbody.empty();
        if (!mappings.length) {
            tbody.append('<tr><td colspan="6" style="text-align:center;color:#999;padding:20px;">No mappings yet. Click "Add New Mapping" to create one.</td></tr>');
            return;
        }
        $.each(mappings, function(idx, m) {
            var fieldLabels = '';
            if (m.fields && m.fields.length) {
                var parts = [];
                $.each(m.fields, function(fi, f) {
                    parts.push(f.label + (f.required ? ' <span style="color:red">*</span>' : ''));
                });
                fieldLabels = parts.join(', ');
            } else {
                fieldLabels = '<em>No fields</em>';
            }
            var row = '<tr data-index="' + idx + '">' +
                '<td class="astro-drag-handle" title="Drag to reorder" style="cursor:move;text-align:center;color:#999;font-size:18px;">&#8597;</td>' +
                '<td><strong>' + escHtml(m.form_type) + '</strong></td>' +
                '<td><code>' + escHtml(m.endpoint) + '</code></td>' +
                '<td>' + escHtml(m.description) + '</td>' +
                '<td>' + fieldLabels + '</td>' +
                '<td>' +
                '<button type="button" class="button button-small astro-edit-mapping" data-index="' + idx + '">Edit</button> ' +
                '<button type="button" class="button button-small astro-delete-mapping" data-index="' + idx + '" style="color:#a00;border-color:#a00;">Delete</button>' +
                '</td>' +
                '</tr>';
            tbody.append(row);
        });
        initSortable();
    }

    function escHtml(str) {
        return $('<div>').text(str || '').html();
    }

    /* =========================================================
       SORTABLE (drag reorder)
    ========================================================= */
    function initSortable() {
        $('#astro-mapping-tbody').sortable({
            handle: '.astro-drag-handle',
            axis: 'y',
            stop: function() {
                var order = [];
                $('#astro-mapping-tbody tr').each(function() {
                    order.push(parseInt($(this).data('index'), 10));
                });
                $.post(ajaxUrl, {
                    action: 'astro_reorder_mappings',
                    nonce: nonce,
                    order: order
                }, function(resp) {
                    if (resp.success) {
                        mappings = reorderArray(mappings, order);
                        rebuildTable();
                    }
                });
            }
        });
    }

    function reorderArray(arr, order) {
        var result = [];
        $.each(order, function(i, idx) {
            if (arr[idx] !== undefined) result.push(arr[idx]);
        });
        return result;
    }

    /* =========================================================
       MODAL
    ========================================================= */
    function openModal(idx) {
        idx = (idx === undefined) ? -1 : parseInt(idx, 10);
        $('#astro-modal-index').val(idx);

        if (idx >= 0 && mappings[idx]) {
            var m = mappings[idx];
            $('#astro-modal-title').text('Edit Mapping: ' + m.form_type);
            $('#astro-modal-form-type').val(m.form_type).prop('readonly', true);
            $('#astro-modal-endpoint').val(m.endpoint);
            $('#astro-modal-description').val(m.description);
            renderFields(m.fields || []);
        } else {
            $('#astro-modal-title').text('Add New Form Type Mapping');
            $('#astro-modal-form-type').val('').prop('readonly', false);
            $('#astro-modal-endpoint').val('');
            $('#astro-modal-description').val('');
            renderFields([]);
        }

        $('#astro-modal-saving').hide();
        $('#astro-modal-overlay').show();
        $('#astro-mapping-modal').css('display', 'flex');
        $('#astro-modal-form-type').focus();
    }

    function closeModal() {
        $('#astro-mapping-modal').hide();
        $('#astro-modal-overlay').hide();
    }

    /* =========================================================
       FIELDS BUILDER
    ========================================================= */
    function renderFields(fields) {
        var container = $('#astro-fields-container');
        container.empty();

        if (!fields.length) {
            container.append('<p id="astro-no-fields" style="color:#999;font-style:italic;">No fields defined yet. Click "+ Add Field" to add one.</p>');
        }

        $.each(fields, function(fi, f) {
            container.append(buildFieldRow(f));
        });

        initFieldSortable();
        bindOptionsToggle();
    }

    function buildFieldRow(f) {
        f = f || {};
        var needsOptions = (f.type === 'select' || f.type === 'radio');
        var optStyle = needsOptions ? '' : 'display:none;';

        var typeOpts = fieldTypeOptions(f.type || 'text');

        var row = $('<div class="astro-field-row" style="background:#f9f9f9;border:1px solid #ddd;border-radius:4px;padding:12px;margin-bottom:8px;position:relative;">' +
            '<span class="astro-field-drag" title="Drag to reorder" style="cursor:move;color:#bbb;font-size:18px;position:absolute;left:10px;top:50%;transform:translateY(-50%);">&#8597;</span>' +
            '<div style="margin-left:28px;">' +
            '<div style="display:grid;grid-template-columns:1fr 1fr 1fr auto auto;gap:10px;align-items:end;">' +

            '<div><label style="font-weight:600;font-size:12px;display:block;margin-bottom:4px;">Field Key *</label>' +
            '<input type="text" class="astro-field-key small-text" value="' + escHtml(f.key || '') + '" placeholder="e.g. birth_date" style="width:100%;" /></div>' +

            '<div><label style="font-weight:600;font-size:12px;display:block;margin-bottom:4px;">Label *</label>' +
            '<input type="text" class="astro-field-label small-text" value="' + escHtml(f.label || '') + '" placeholder="e.g. Date of Birth" style="width:100%;" /></div>' +

            '<div><label style="font-weight:600;font-size:12px;display:block;margin-bottom:4px;">Type</label>' +
            '<select class="astro-field-type" style="width:100%;">' + typeOpts + '</select></div>' +

            '<div style="text-align:center;"><label style="font-weight:600;font-size:12px;display:block;margin-bottom:4px;">Required</label>' +
            '<input type="checkbox" class="astro-field-required" ' + (f.required ? 'checked' : '') + ' style="width:18px;height:18px;" /></div>' +

            '<div><label style="font-size:12px;">&nbsp;</label>' +
            '<button type="button" class="button button-small astro-remove-field" style="color:#a00;border-color:#a00;display:block;">&#10005;</button></div>' +

            '</div>' + // grid

            '<div class="astro-field-options-wrap" style="margin-top:8px;' + optStyle + '">' +
            '<label style="font-weight:600;font-size:12px;display:block;margin-bottom:4px;">Options <span style="font-weight:normal">(value|Label, one per line)</span></label>' +
            '<textarea class="astro-field-options" rows="3" style="width:100%;font-family:monospace;font-size:12px;" placeholder="aries|Aries&#10;taurus|Taurus">' + escHtml(f.options || '') + '</textarea>' +
            '</div>' +

            '</div>' + // margin-left
            '</div>');

        return row;
    }

    function initFieldSortable() {
        $('#astro-fields-container').sortable({
            handle: '.astro-field-drag',
            axis: 'y'
        });
    }

    function bindOptionsToggle() {
        $('#astro-fields-container').off('change', '.astro-field-type').on('change', '.astro-field-type', function() {
            var val = $(this).val();
            var wrap = $(this).closest('.astro-field-row').find('.astro-field-options-wrap');
            if (val === 'select' || val === 'radio') { wrap.show(); } else { wrap.hide(); }
        });
    }

    /* =========================================================
       COLLECT FIELDS FROM MODAL
    ========================================================= */
    function collectFields() {
        var fields = [];
        $('#astro-fields-container .astro-field-row').each(function() {
            var key = $(this).find('.astro-field-key').val().trim().replace(/\s+/g, '_');
            if (!key) return;
            fields.push({
                key:      key,
                label:    $(this).find('.astro-field-label').val().trim() || key,
                type:     $(this).find('.astro-field-type').val(),
                required: $(this).find('.astro-field-required').is(':checked') ? 1 : 0,
                options:  $(this).find('.astro-field-options').val().trim()
            });
        });
        return fields;
    }

    /* =========================================================
       EVENT BINDINGS
    ========================================================= */
    $(document).ready(function() {

        // Init sortable on load
        initSortable();

        // Add new mapping
        $('#astro-add-mapping').on('click', function() { openModal(); });

        // Edit mapping
        $(document).on('click', '.astro-edit-mapping', function() { openModal($(this).data('index')); });

        // Delete mapping
        $(document).on('click', '.astro-delete-mapping', function() {
            var idx = parseInt($(this).data('index'), 10);
            if (!confirm('Delete this mapping?')) return;
            $.post(ajaxUrl, { action: 'astro_delete_mapping', nonce: nonce, index: idx }, function(resp) {
                if (resp.success) { showNotice(resp.data.message, 'success'); rebuildTable(resp.data.mappings); }
                else { showNotice(resp.data.message, 'error'); }
            });
        });

        // Close modal
        $('#astro-modal-close, #astro-modal-cancel, #astro-modal-overlay').on('click', function(e) {
            if ($(e.target).is('#astro-modal-overlay') || !$(e.target).closest('#astro-mapping-modal').length || $(e.target).is('#astro-modal-close') || $(e.target).is('#astro-modal-cancel')) {
                closeModal();
            }
        });

        // Add field button
        $('#astro-add-field').on('click', function() {
            $('#astro-no-fields').remove();
            $('#astro-fields-container').append(buildFieldRow({}));
            initFieldSortable();
            bindOptionsToggle();
        });

        // Remove field
        $(document).on('click', '.astro-remove-field', function() {
            if (!confirm('Remove this field?')) return;
            $(this).closest('.astro-field-row').remove();
            if (!$('#astro-fields-container .astro-field-row').length) {
                $('#astro-fields-container').append('<p id="astro-no-fields" style="color:#999;font-style:italic;">No fields. Click "+ Add Field".</p>');
            }
        });

        // Save mapping
        $('#astro-modal-save').on('click', function() {
            var formType   = $('#astro-modal-form-type').val().trim().replace(/\s+/g, '_');
            var endpoint   = $('#astro-modal-endpoint').val().trim();
            var description = $('#astro-modal-description').val().trim();
            var index      = parseInt($('#astro-modal-index').val(), 10);
            var fields     = collectFields();

            if (!formType) { alert('Form Type Key is required.'); return; }
            if (!endpoint) { alert('API Endpoint is required.'); return; }

            $('#astro-modal-saving').show();
            $('#astro-modal-save').prop('disabled', true);

            $.post(ajaxUrl, {
                action: 'astro_save_mapping',
                nonce: nonce,
                index: index,
                form_type: formType,
                endpoint: endpoint,
                description: description,
                fields: fields
            }, function(resp) {
                $('#astro-modal-saving').hide();
                $('#astro-modal-save').prop('disabled', false);
                if (resp.success) {
                    showNotice(resp.data.message, 'success');
                    rebuildTable(resp.data.mappings);
                    closeModal();
                } else {
                    alert(resp.data.message || 'Error saving mapping.');
                }
            }).fail(function() {
                $('#astro-modal-saving').hide();
                $('#astro-modal-save').prop('disabled', false);
                alert('AJAX request failed.');
            });
        });

        // Test API connection
        $('#astro-test-connection').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).text('Testing...');
            $.post(ajaxUrl, {
                action: 'astro_test_api_connection',
                nonce: nonce,
                user_id: $('#api_user_id').val(),
                api_key: $('#api_key').val(),
                api_url: $('#api_url').val()
            }, function(resp) {
                btn.prop('disabled', false).text('Test API Connection');
                var cls = resp.success ? 'notice-success' : 'notice-error';
                $('#astro-test-result').html('<div class="notice ' + cls + '"><p>' + resp.data.message + '</p></div>');
            });
        });

    });

})(jQuery);
