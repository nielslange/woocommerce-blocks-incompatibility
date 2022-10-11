# WooCommerce Blocks Incompatibility Notice

While the initial cart and checkout pages of WooCommerce are based on PHP, the new cart and checkout pages of WooCommerce Blocks are based on React. As a result of that, not every extension, that is compatible to WooCommerce, is also compatible to WooCommerce Blocks.

This plugin in a proof of concern to display a warning message on the admin dashboard, if an extension gets activated that is incompatible to WooCommerce Blocks.

## Core

The core of this proof of concept plugin takes place in https://github.com/nielslange/woocommerce-blocks-incompatibility-notice/blob/afe68fe32641975755190ddf5df08f1500045d28/woocommerce-blocks-incompatibility-notice.php#L47-L62

This section gets its data from [extensions.json](https://github.com/nielslange/woocommerce-blocks-incompatibility-notice/blob/trunk/extensions.json): https://github.com/nielslange/woocommerce-blocks-incompatibility-notice/blob/8799ceb3358e5b08edc70f3a597534d8fc2275b5/extensions.json#L1-L12

As seen in the code snippet above, the JSON file contains the text domains of incompatible extensions, divided into breaking and non-breaking extensions.

## Screenshots

### Screen with both breaking and non-breaking extensions and closed notice box

![incompatibility-notice-closed-notice-section](https://user-images.githubusercontent.com/3323310/194984026-473d080e-0b68-4c55-aa14-df1d145ab870.png)

### Screen with both breaking and non-breaking extensions and open notice box

![incompatibility-notice-open-notice-section](https://user-images.githubusercontent.com/3323310/194984031-c09b322a-be09-469e-9564-42bcf99a3116.png)
