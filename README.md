# Yireo GoogleTagManager
Magento extension for implementing Google Tag Manager in your Magento theme.

More information: https://www.yireo.com/software/magento-extensions/google-tag-manager

## Requirements

1) PHP > 7
2) Magento > 1.6

## Installation
You can install this module in various ways:

1) Download the MagentoConnect package from our site and upload it into your own Magento
Downloader application.

2) Download the Magento source archive from our site, extract the files and upload the
files to your Magento root. Make sure to flush the Magento cache. Make sure to logout 
once you're done.

3) Use modman to install the git repository for you:

```
modman init
modman clone https://github.com/yireo/Yireo_GoogleTagManager
modman update Yireo_GoogleTagManager
```

4) Using composer (recommended - see below)

## Instructions for using composer

Use composer to install this extension. Before you can do this under Magento 1, you need to install the composer installer first:

    composer require magento-hackathon/magento-composer-installer

Make sure to set the `magento-root-dir` properly. Test this by running:

    composer update --no-dev

Once the composer installer is correctly setup, you can install our extension:    

    composer require yireo/magento1-googletagmanager

Done.

## Configuration
Login to your [Google Tag Manager](http://www.google.com/tagmanager/) account. Follow the Google instructions to create a new Google Tag Manager **container**. Extract the **Container Public ID** from your new container. Configure your new ID in Magento through **System > Configuration > [Services] Google Tag Manager**.

## Features
The extension has the following configuration options:

- **Enabled**: When this is set to No, the extension does not work.
- **Container Public ID**: The ID of your Google Tags container
- **Insertion Method**: A technical thing which is either set to *Observer* or to *XML Layout*. If the one does not work for you, try the other one instead.
- **Debug**: For developers.

## Data layer attributes
This extension adds the following attributes automatically to the data layer. There is no backend option to tune this, so if you want to make changes, change the PHTML templates in your own theme.

- `customerLoggedIn` = Whether the customer has logged in
- `customerId` = ID of the customer
- `customerGroupId` = Customer group ID
- `customerGroupCode` = Customer group code
- `categoryId` = Category ID
- `categoryName` = Category name
- `categoryProducts` = Listing of top 3 products
- `productId` = Product ID
- `productName` = Product name
- `productSku` = Product SKU
- `productPrice` = Product price
- `transactionId` = Order ID
- `transactionDate` = Order date
- `transactionAffiliation` = Website name
- `transactionTotal` = Order total
- `transactionTax` = Order tax
- `transactionShipping` = Shipping amount
- `transactionPayment` = Payment method
- `transactionCurrency` = Currency of website
- `transactionPromoCode` = Promotional code used for order
- `transactionProducts` = Listing of all items in cart
    - `sku` = Item SKU
    - `name` = Item name
    - `price` = Item price
    - `category` = Listing of category IDs
    - `quantity` = Item quantity

Note that on a product page with an URL that also identifies a category, both the product attributes and the category attributes are added.

## Technical details
This module does not use XML rewrites to change core classes. It uses two insertion modes - one using the XML layout, one using an observed event `core_block_abstract_to_html_after`.

### Tip
When you want to track conversions in your Magento checkout, our extension helps out as well: It adds the relevant information to all your checkout and cart pages. Do you want to know which variables are on the success page? Use the Yireo CheckoutTester extension to preview that page and view its HTML source.

Bring your towel.
