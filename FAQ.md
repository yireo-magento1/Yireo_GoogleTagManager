# Does this module support Enhanced Ecommerce (UA)?
Yes, it does. The module pushes useful information to various parts of the GTM data layer. However, to track all possible information, various JavaScript events need to be added to numerous places of your Magento theme. For instance, product clicks (onclick="dataLayer.push({'event': 'productClick'});") need to be added everywhere where your Magento theme contains a product link. Because this would replace 99% of all your current theme, and this would increase problems dramatically, we have choosen to add all the elements of the data layer, but you will need to add the events manually to your code.

If you want to hire us for adding these events, please let us know. If you want to customize things yourself, make sure to check the comments in the PHTML templates of our extension.

# I don't see any JavaScript or HTML in your PHTML templates
This is correct. With our module, PHTML templates are not used to generate the actual HTML and JavaScript code. Instead, they allow you to tune PHP arrays that will be converted automatically into the right JSON data that will be passed onto the GTM data layer. Using PHTML templates, you have the benefit of theme inheritance. The PHP arrays are simple and it should be easy to customize them, provided you have the right knowledge of PHP.
