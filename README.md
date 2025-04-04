
Photogallery module
=====

This module enables you to manage photo galleries on your site, with the ability to create nested photo albums. To get started, create at least one album. Once that's done, you can begin adding photos to the album. In the album's template, you can easily loop through an array of photo entities to display the images.

## Available methods

You only need a single template, named `album.phtml`. Here are the methods you'll be using:

    $photo->getName(); // Returns photo name
    $photo->getDescription(); //Returns photo description
    $photo->getImageUrl($dimension); // Returns URL path to a photo with provided dimension



## Nested albums

To check if an album has child albums, simply verify if the `$albums` variable exists. If it does, it will be an array of child album entities; otherwise, the album has no children. You can check this using the `isset()` function, like so:
 

    <div>
     <?php if (isset($albums)): ?>
       // There are child albums, do iterate over this array somewhere
     <?php endif; ?>
    </div>


## Example: Rendering album photos

Note: This example assumes you've configured the image dimensions to 250x250 in the settings.

    <?php if (!empty($photos)): ?>
    <div class="row">
	    <?php foreach ($photos as $photo): ?>
	     <div class="col-lg-4">
	       <img src="<?= $photo->getImageUrl('250x250'); ?>" alt="<?= $photo->getName(); ?>" />
	     </div>
	    <?php endforeach; ?>
    </div>
    <?php endif; ?>

## Site service

A pre-defined `$photogallery` service is also available if you need to render gallery members on a different page. For example, you might use it on a landing page to display all photos from a specific album. The `$photogallery` service provides a single method for this purpose:

### getAll($id)

Returns an array of photo entities by given album id.

As you might have already guessed, the usage is the same. Instead of rendering the `$photos` array as shown in the previous example, simply substitute it with `$photogallery->getAll('..some id..')`.