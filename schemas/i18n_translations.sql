-- PostgreSQL

CREATE TABLE i18n_translations (
    id                  bigint NOT NULL DEFAULT nextval('i18n_translations_id_seq'::regclass),
    uuid                uuid DEFAULT gen_random_uuid(),
    hash                text NOT NULL,
    common_language_id  bigint,
    text                text NOT NULL,
    translation         text NOT NULL,
    common_domain_id    bigint,
    CONSTRAINT i18n_translations_pkey PRIMARY KEY (id)
);
