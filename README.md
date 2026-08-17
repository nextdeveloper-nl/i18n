# NextDeveloper i18n Module
This is a very simple module to maintain internationalization features of a project.

In this current version we are planning to provide features below;
- Dynamic Internationalization Storage
- AI Supported Translation

## Features
### Dynamic Internationalization Storage
We will be storing the internationalized sentences or messages by using primary, secondary and backup storage systems.

In the very first version we are planning to use redis as primary storage and mysql as secondary storage. However for various different reasons we think that we may need json based file storage too, that is why we will be supporting json file storage too.

### AI Supported Translation
We believe that in case we need for a certain language to communicate, we need to support it. That is why in the database we will be adding translations in terms of source_id and language_id. So in case a new language is required the application will automatically populate the translations using AI translation engine to the related language required.

# Want to contribute?
You can always be in our team which creates magnificant projects! Just email to support@plusclouds.com



---

## Our Libraries

This library is part of the **NextDeveloper / PlusClouds open-source ecosystem**. Browse all available libraries and find the right building blocks for your next project:

[https://plusclouds.com/us/solutions/libraries](https://plusclouds.com/us/solutions/libraries)

---

## Join the Community

We believe great software is built together. The PlusClouds developer community is a place where engineers share ideas, ask questions, showcase what they have built, and help shape the direction of these libraries. Whether you are integrating a single package or building an entire platform on top of our stack, you are very welcome here.

Come and join us — we would love to see what you build:

[https://plusclouds.com/us/community](https://plusclouds.com/us/community)
