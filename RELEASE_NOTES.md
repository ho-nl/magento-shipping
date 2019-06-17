Release Notes
=============
1.0.5
-----
Refactoring:

* Renamed the API folder conform the Magento standard (Camelcase).

1.0.4
-----
Added logic:

* The carrier now shows the weekend day title/description when the cut-off is reached and the next day is a weekend day.

1.0.3
-----
Minor bugfix:

* Errors that are thrown during shipment/label creation are now thrown properly as a string.

1.0.2
-----
Added functionality:

* Added configuration for allowed countries for which the shipping method is available.
* Set Netherlands as the default allowed country.

1.0.1
-----
Compatibility fix:

* Made the tracking mail service backwards compatible with Magento 2.2 versions (sendFromByScope does not exist below 2.3).

1.0.0
-----
Initial public version of the module. Ships with the following features:

* Manage API configuration.
* Manage shipping method configuration.
* Confirm shipments.
* Get shipping labels / track and trace codes.
* Download shipping labels.
* Send tracking mails (optional: automatically send after adding a track and trace code). 
