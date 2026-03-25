-- =============================================================================
-- Si al "Denegar eliminación" ves: Unknown column 'deletion_resolution'
--
-- MÁS FÁCIL (desde la carpeta del proyecto, con PHP de XAMPP):
--   c:\xampp\php\php.exe artisan crm:fix-deletion-columns
--
-- Alternativa: php artisan migrate
-- Alternativa: ejecutar este SQL en phpMyAdmin (base crm_nv)
-- =============================================================================
-- Comprueba antes si ya existen las columnas; si alguna existe, omite solo esa línea.

ALTER TABLE `contacts`
    ADD COLUMN `deletion_resolution` VARCHAR(20) NULL DEFAULT NULL AFTER `deletion_reason`,
    ADD COLUMN `deletion_resolution_note` TEXT NULL AFTER `deletion_resolution`,
    ADD COLUMN `deletion_resolved_at` TIMESTAMP NULL DEFAULT NULL AFTER `deletion_resolution_note`,
    ADD COLUMN `deletion_resolved_by` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `deletion_resolved_at`,
    ADD COLUMN `deletion_decision_user_id` BIGINT UNSIGNED NULL DEFAULT NULL AFTER `deletion_resolved_by`;

ALTER TABLE `contacts`
    ADD CONSTRAINT `contacts_deletion_resolved_by_foreign`
        FOREIGN KEY (`deletion_resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    ADD CONSTRAINT `contacts_deletion_decision_user_id_foreign`
        FOREIGN KEY (`deletion_decision_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Si MySQL dice que `deletion_reason` no existe, quita "AFTER `deletion_reason`" de la primera línea
-- (deja solo ADD COLUMN ... sin AFTER).
