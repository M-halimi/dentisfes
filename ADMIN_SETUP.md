# Admin rendez-vous

L’espace admin reste volontairement limité aux demandes de rendez-vous.

## Configuration

Ajouter dans `.env` :

```dotenv
ADMIN_EMAIL="admin@example.test"
ADMIN_PASSWORD="change-this-to-a-long-secret"
```

Ces valeurs ne doivent pas être commitées ni utilisées comme identifiants définitifs en production.

## Accès

- Connexion : `/admin/connexion`
- Tableau : `/admin`
- Déconnexion : bouton dans le tableau

## Actions disponibles

- Voir les demandes paginées
- Voir nom, téléphone, email, soin, date, heure et message
- Passer le statut à `pending`, `confirmed` ou `cancelled`
- Supprimer une demande

Les routes admin sont protégées par session, CSRF et une limite de 5 tentatives par minute sur la connexion.
