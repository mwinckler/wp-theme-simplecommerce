*This theme is a work-in-progress. It is not yet production-ready.*

# SimpleCommerce WordPress theme

The goal of this theme is to provide a simple theme suitable for a commerce-based website. Features include:

* Customizable header image
* Shortcodes
* Responsive, mobile-friendly layout

## Shortcodes

### Columns

Use the `[columns]` shortcode to indicate which content should be broken up in to columns, and then within that denote each column using a `[column]` shortcode. The theme supports 2- and 3-column layouts. Example:

	[columns]
	[column]
	This text goes in the first column.
	[/column]
	[column]
	This would go in the second column.
	[/column]
	[/columns]

### Testimonial Boxes

The `[testimonial]` shortcode generates a box to quote someone with a fancy attribution. It supports the following attributes:

* `name`: The name to be displayed (e.g. a person or publication name).
* `url`: If provided, the name will be linked to this URL. If `name` is not provided, `url` has no effect.
* `image_url`: An image to display alongside the name. Ideally, this image will be 80px square.

Example:

	[testimonial name="Matt Winckler" url="http://mattwinckler.com" image_url="//lh5.googleusercontent.com/-tLwbqygLOSc/AAAAAAAAAAI/AAAAAAAAAAA/GbHKSpB8i0w/s96-c-mo/photo.jpg"]
	This WordPress theme is the cat's pajamas!
	[/testimonial]

### Toggled Content

Use `[toggle]` to allow users to show or hide the content contained in the shortcode. Supported attributes:

* `title` (required): The text to display in the clickable section to expand/collapse the content.
* `initial_state` (optional): Indicates whether the toggled content should be visible or hidden on page load. Allowed values: `open`, `closed`. Defaults to `closed`.

## Images

By default, images within an `article` tag (used by posts and pages) are given a small drop shadow. To eliminate this, add the `noshadow` CSS class to the image declaration. Example:

	<img src="myimage.png" alt="Beautiful, and with no shadow" class="noshadow" />

