-- Exemple de requêtes INSERT générées pour la table users
-- Note: Remplacez les valeurs par vos données réelles

INSERT INTO `users`(`id`, `matricule`, `name`, `first_name`, `fonction`, `lieu_affectation`, `zone_affectation`, `direction`, `numero_flotte`, `email`, `email_verified_at`, `password`, `role`, `active`, `avatar_url`, `remember_token`, `created_at`, `updated_at`) 
VALUES 
(1,'M0001','Dupont','Jean','Manager','Antananarivo','Zone Nord','Direction Technique','0341234567','jean.dupont@acepmg.mg',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user',1,NULL,NULL,'2024-01-01 00:00:00','2024-01-01 00:00:00'),
(2,'M0002','Martin','Marie','Assistant','Antananarivo','Zone Sud','Direction Commerciale','0341234568','marie.martin@acepmg.mg',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user',1,NULL,NULL,'2024-01-01 00:00:00','2024-01-01 00:00:00');

-- Pour générer le mot de passe hashé pour '@Zerty123', utilisez :
-- php artisan tinker
-- Hash::make('@Zerty123')

-- Exemple avec mot de passe hashé pour '@Zerty123' :
INSERT INTO `users`(`id`, `matricule`, `name`, `first_name`, `fonction`, `lieu_affectation`, `zone_affectation`, `direction`, `numero_flotte`, `email`, `email_verified_at`, `password`, `role`, `active`, `avatar_url`, `remember_token`, `created_at`, `updated_at`) 
VALUES 
(1,'M0001','Dupont','Jean','Manager','Antananarivo','Zone Nord','Direction Technique','0341234567','jean.dupont@acepmg.mg',NULL,'$2y$10$[HASH_GENERATED]','user',1,NULL,NULL,NOW(),NOW());

