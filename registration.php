<?php

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Cream_RedJePakketje',
    sprintf('%s/src', __DIR__)
);
