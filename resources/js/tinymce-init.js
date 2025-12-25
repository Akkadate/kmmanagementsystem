import tinymce from 'tinymce/tinymce';
import 'tinymce/models/dom';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/media';
import 'tinymce/plugins/table';
import 'tinymce/plugins/wordcount';

// Import skins
import contentUiCss from 'tinymce/skins/ui/oxide/content.css?inline';
import contentCss from 'tinymce/skins/content/default/content.css?inline';
import skinCss from 'tinymce/skins/ui/oxide/skin.css?inline';

// Initialize TinyMCE when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const contentField = document.getElementById('content');

    if (contentField) {
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: false,
            skin: false,
            content_css: false,
            content_style: [contentUiCss, contentCss, 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'].join('\n'),
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | code',
            promotion: false,
            branding: false,
            license_key: 'gpl',
            language: 'en',
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('TinyMCE editor initialized successfully (self-hosted)');
                });

                // Sync content to textarea before form submission
                editor.on('change', function() {
                    editor.save();
                });
            }
        });

        // Ensure content is saved before form submit
        const form = contentField.closest('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Trigger TinyMCE to save content to textarea
                if (tinymce.get('content')) {
                    tinymce.get('content').save();
                }
            });
        }
    }
});

// Inject skin CSS
const styleElement = document.createElement('style');
styleElement.textContent = skinCss;
document.head.appendChild(styleElement);
