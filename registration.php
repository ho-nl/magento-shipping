<?php

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'RedJePakketje_Shipping',
    sprintf('%s/src', __DIR__)
);
