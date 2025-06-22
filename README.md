
Photogallery module
=====

This module enables you to manage photo galleries on your site, with the ability to create nested photo albums. To get started, create at least one album. Once that's done, you can begin adding photos to the album. In the album's template, you can easily loop through an array of photo entities to display the images.

## Available methods

You only need a single template file named `album.phtml`.  Within this template, a variable called `$photos` is available, containing an array of photo entities. Each photo entity provides the following methods:

    $photo->getName();  // Returns the photo's name
    $photo->getDescription(); // Returns the photo's description
    $photo->getImageUrl($dimension); // Returns the URL of the photo at the specified dimension

## Nested albums

To check if an album has child albums, simply verify if the `$albums` variable exists. If it does, it will be an array of child album entities; otherwise, the album has no children. You can check this using the `isset()` function, like so:
 

    <?php
    
    // Shared size for photos and album covers
    $size = '904x621';
    
    ?>
    
    <section class="py-5">
        <div class="container py-5">
         <h2 class="my-4"><?= $page->getName(); ?></h2>
         
         <?php if (isset($albums)): ?>
         <div class="row">
             <?php foreach ($albums as $child): ?>
             <div class="col-lg-4">
               <a href="<?= $child->getUrl(); ?>">
                  <img class="d-block mb-3 img-fluid" title="<?= $child->getName(); ?>" src="<?= $child->getImageUrl($size); ?>">
               </a>
               <a class="text-dark text-decoration-none" href="<?= $child->getUrl(); ?>"><?= $child->getName(); ?></a>
             </div>
             <?php endforeach?>
         </div>
         <?php else: ?>
         <div class="row gx-3 gy-3">
            <?php foreach ($photos as $photo): ?>
             <div class="col-lg-4">
               <img class="img-fluid" src="<?= $photo->getImageUrl($size); ?>" alt="<?= $photo->getName(); ?>" />
             </div>
            <?php endforeach; ?>
         </div>
         <?php endif; ?>
        </div>
    </section>

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

## URL Generation

To generate a URL for an album by its ID (assuming the album ID is 1), use:

    <a href="<?= $cms->createUrl(1, 'Photogallery'); ?>">View album</a>

## Site service

A pre-defined `$photogallery` service is also available if you need to render gallery members on a different page. For example, you might use it on a landing page to display all photos from a specific album. The `$photogallery` service provides a single method for this purpose:

### Getting all photos by album ID

Returns an array of photo entities for the specified album ID.

Usage is similar to the previous example. Instead of using the `$photos` array directly, simply replace it with the result of:

`$photogallery->getAllByAlbumId('..some id..')`

### Getting child albums

To retrieve child album entries, use the following method:

`$photogallery->getChildAlbums($id, $limit = null)`

-   The first argument, `$id`, is the ID of the parent album. 
-   The second argument, `$limit`, optionally restricts the number of returned entries.

