// image processing for profile upload
// https://github.com/fengyuanchen/cropper
$( document ).ready(function() {

	function processImage(input, selector, w, h) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			var image = $('.'+selector+' #image');

			reader.onload = function (e) {
				// Destroy cropper
				image.cropper('destroy');

				// set allowed extensions
				var allowedExt = ['jpg','jpeg','png','gif'];
				// get extension from base64 image
				var img = e.target.result.split(';');
				img = img[0].split('/');
				// check if base64 image extension is allowed
				if(allowedExt.indexOf(img[1]) == -1) {
					UIkit.notification({
						message: 'Extension not allowed!',
						status: 'warning',
						pos: 'top-center',
						timeout: 5000
					});
					$('.error-'+selector).html('');
					//empty hidden field value so it doesn't get proccesed by php
					document.getElementById('hidden-'+selector).value = '';
					//empty file field value so it doesn't get proccesed by php
					document.getElementById(selector).value = '';
				} else {
					// Replace url
					image.attr('src', e.target.result);
					$('.'+selector+' #image').attr('src', e.target.result);
					$('.'+selector).css({
						'width': w,
						'height': h,
					});
					$('#current-'+selector).css({
						'display': 'none',
					});
					$('.'+selector).css({
						'display': 'block',
					});
					// save base64 image in hidden field
					document.getElementById('hidden-'+selector).value = e.target.result;
					$('.error-'+selector).html('');
					image.cropper({
						viewMode: 3,
						autoCropArea: 1,
						aspectRatio: w/h,
						minCropBoxWidth: w,
						minCropBoxHeight: h,
						minContainerWidth: w,
						minContainerHeight: h,
						built: function () {
							$('.'+selector+' #image').cropper('setCropBoxData', { width: w, height: h });
						}
					});
					//update image when dragging
					image.on('cropend.cropper', function (e) {
						document.getElementById('hidden-'+selector).value = image.cropper('getCroppedCanvas').toDataURL('image/jpeg');
						$('.'+selector+' #image').attr('src', image.cropper('getCroppedCanvas').toDataURL('image/jpeg'));
					});
					//update image when zooming
					image.on('zoom.cropper', function (e) {
						document.getElementById('hidden-'+selector).value = image.cropper('getCroppedCanvas').toDataURL('image/jpeg');
						$('.'+selector+' #image').attr('src', image.cropper('getCroppedCanvas').toDataURL('image/jpeg'));
					});
				}
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$('#profile-image').change(function(){
		processImage(this, 'profile-image', 190, 190);
	});
});
