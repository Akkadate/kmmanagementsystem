import tinymce from 'tinymce/tinymce';

// Import TinyMCE theme
import 'tinymce/themes/silver';

// Import TinyMCE icons
import 'tinymce/icons/default';

// Import TinyMCE plugins
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
import 'tinymce/plugins/help';
import 'tinymce/plugins/wordcount';

// Initialize TinyMCE when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const contentField = document.getElementById('content');

    if (contentField) {
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | code | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            promotion: false,
            branding: false,
            license_key: 'gpl',
            skin: false,
            content_css: false,
            setup: function(editor) {
                editor.on('init', function() {
                    console.log('TinyMCE editor initialized successfully (self-hosted)');
                });
            }
        });
    }
});
