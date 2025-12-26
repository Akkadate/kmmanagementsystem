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
                'link image | removeformat | code',
            promotion: false,
            branding: false,
            license_key: 'gpl',
            language: 'en',
            // Image upload settings
            images_upload_url: '/admin/articles/upload-image',
            images_upload_handler: function (blobInfo, progress) {
                return new Promise(function (resolve, reject) {
                    console.log('Starting image upload:', blobInfo.filename());

                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '/admin/articles/upload-image');

                    // Add CSRF token
                    const token = document.querySelector('meta[name="csrf-token"]');
                    if (token) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', token.getAttribute('content'));
                        console.log('CSRF token added');
                    } else {
                        console.error('CSRF token not found!');
                    }

                    xhr.upload.onprogress = function (e) {
                        const percentage = (e.loaded / e.total * 100);
                        progress(percentage);
                        console.log('Upload progress:', percentage.toFixed(2) + '%');
                    };

                    xhr.onload = function () {
                        console.log('Upload completed. Status:', xhr.status);
                        console.log('Response:', xhr.responseText);

                        if (xhr.status === 403) {
                            console.error('403 Forbidden - CSRF token issue?');
                            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                            return;
                        }

                        if (xhr.status < 200 || xhr.status >= 300) {
                            console.error('HTTP Error:', xhr.status);
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }

                        try {
                            const json = JSON.parse(xhr.responseText);
                            console.log('Parsed JSON:', json);

                            if (!json || typeof json.location != 'string') {
                                console.error('Invalid JSON response:', xhr.responseText);
                                reject('Invalid JSON: ' + xhr.responseText);
                                return;
                            }

                            console.log('Image uploaded successfully:', json.location);
                            resolve(json.location);
                        } catch (error) {
                            console.error('JSON parse error:', error);
                            reject('Failed to parse response: ' + error.message);
                        }
                    };

                    xhr.onerror = function () {
                        console.error('XHR error. Status:', xhr.status);
                        reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());

                    console.log('Sending upload request...');
                    xhr.send(formData);
                });
            },
            automatic_uploads: false,
            file_picker_types: 'image',
            file_picker_callback: function (callback, value, meta) {
                if (meta.filetype === 'image') {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.onchange = function () {
                        const file = this.files[0];

                        if (!file) {
                            return;
                        }

                        console.log('File selected:', file.name);

                        // Upload immediately
                        const formData = new FormData();
                        formData.append('file', file);

                        const xhr = new XMLHttpRequest();
                        xhr.open('POST', '/admin/articles/upload-image');

                        // Add CSRF token
                        const token = document.querySelector('meta[name="csrf-token"]');
                        if (token) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', token.getAttribute('content'));
                        }

                        xhr.onload = function () {
                            console.log('Upload response status:', xhr.status);
                            console.log('Upload response text:', xhr.responseText);

                            if (xhr.status === 200) {
                                try {
                                    const json = JSON.parse(xhr.responseText);
                                    console.log('Image uploaded:', json.location);
                                    // Insert the uploaded image
                                    callback(json.location, { title: file.name });
                                } catch (error) {
                                    console.error('Failed to parse response:', error);
                                    window.Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Failed',
                                        text: 'Invalid response from server'
                                    });
                                }
                            } else {
                                console.error('Upload failed with status:', xhr.status);
                                console.error('Error response:', xhr.responseText);
                                window.Swal.fire({
                                    icon: 'error',
                                    title: 'Upload Failed',
                                    text: 'Failed to upload image: ' + xhr.status
                                });
                            }
                        };

                        xhr.onerror = function () {
                            console.error('Upload error');
                            window.Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                text: 'Failed to upload image'
                            });
                        };

                        console.log('Uploading image...');
                        xhr.send(formData);
                    };

                    input.click();
                }
            },
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
            let isUploading = false;

            form.addEventListener('submit', function(e) {
                const editor = tinymce.get('content');
                if (editor && !isUploading) {
                    // Save content to textarea first
                    editor.save();

                    // Check if there are pending images to upload
                    const hasImages = editor.getContent().includes('blob:');

                    if (hasImages) {
                        e.preventDefault();
                        isUploading = true;

                        console.log('Uploading images before submit...');

                        // Upload all images first
                        editor.uploadImages().then(function() {
                            console.log('All images uploaded, saving and submitting...');
                            // Save content again after upload
                            editor.save();
                            isUploading = false;
                            // Submit the form programmatically
                            form.submit();
                        }).catch(function(error) {
                            console.error('Image upload failed:', error);
                            window.Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                text: 'Failed to upload images. Please try again.'
                            });
                            isUploading = false;
                        });
                    }
                }
            });
        }
    }
});

// Inject skin CSS
const styleElement = document.createElement('style');
styleElement.textContent = skinCss;
document.head.appendChild(styleElement);
