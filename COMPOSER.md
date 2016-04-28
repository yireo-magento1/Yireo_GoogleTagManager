# Instructions for using composer

Use composer to install this extension. First make sure to initialize composer with the right settings:

    composer -n init
    composer install --no-dev

Next, modify your local composer.json file:

    {
        "require": {
            "yireo/yireo_googletagmanager": "dev-master",
            "magento-hackathon/magento-composer-installer": "*"
        },    
        "repositories":[
            {
                "packagist": false
            },
            {
                "type":"composer",
                "url":"https://packages.firegento.com"
            },
            {
                "type":"composer",
                "url":"https://satis.yireo.com"
            }
        ],
        "extra":{
            "magento-root-dir":"/path/to/magento",
            "magento-deploystrategy":"copy"           
        }
    }

Make sure to set the `magento-root-dir` properly. Test this by running:

    composer update --no-dev

Done.

