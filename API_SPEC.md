# CSNDR — Spécification API REST

Base URL: `${REACT_APP_API_URL}` côté front. Toutes les réponses sont JSON. Authentification par Bearer Token (Sanctum). Pagination Laravel standard.

## Conventions générales
- Headers: `Authorization: Bearer <token>` pour endpoints protégés
- Format succès: `{ "data": ..., "meta": { ... } }`
- Format erreur: `{ "errors": [{ "code": "...", "message": "...", "fields": { ... } }] }`
- Codes: 200 OK, 201 Created, 204 No Content, 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found, 422 Unprocessable Entity, 500 Server Error

---

## Auth
### POST /api/auth/login
Body:
```json
{ "email": "user@example.com", "password": "secret" }
```
Responses:
- 200:
```json
{ "data": { "token": "<JWT_OR_PLAIN_TOKEN>", "user": { "id": 1, "name": "...", "email": "...", "role": "enseignant" } } }
```
- 401: identifiants invalides

### POST /api/auth/logout
Headers: Authorization
- 204 No Content

### GET /api/auth/me
Headers: Authorization
- 200:
```json
{ "data": { "id": 1, "name": "...", "email": "...", "role": "parent" } }
```

---

## Users
### GET /api/users?role=admin|enseignant|parent|eleve&page=1
- 200:
```json
{ "data": [{ "id":1, "name":"...", "email":"...", "role":"eleve" }], "meta": { "current_page":1, "total": 42 } }
```

### POST /api/users (admin)
Body:
```json
{ "name":"...", "email":"...", "password":"...", "role":"enseignant" }
```
- 201: `{ "data": { "id": 10, ... } }`
- 422: validation

### PATCH /api/users/{id}
Body (ex):
```json
{ "name":"Nouveau Nom", "role":"parent" }
```
- 200: `{ "data": { ... } }`

### DELETE /api/users/{id}
- 204

### POST /api/users/{id}/link-eleve (lier parent↔enfant)
Body:
```json
{ "eleve_id": 33 }
```
- 204

---

## Classes
### GET /api/classes
- 200:
```json
{ "data": [{ "id":1, "nom":"CM2 A", "niveau":"CM2", "annee_scolaire":"2024-2025", "professeur": {"id":5, "name":"Mme X"} } ] }
```

### POST /api/classes (admin)
Body:
```json
{ "nom":"CM1 B", "niveau":"CM1", "annee_scolaire":"2024-2025", "professeur_id":5 }
```
- 201

### POST /api/classes/{id}/eleves (affectations)
Body:
```json
{ "eleves": [11, 12, 13] }
```
- 204

---

## Devoirs
### GET /api/devoirs?classe_id=1&date_min=2025-09-01&date_max=2025-09-30
- 200:
```json
{ "data": [{ "id":1, "classe_id":1, "titre":"Dictée", "date_rendu":"2025-09-15", "fichier_url": null }] }
```

### POST /api/devoirs (enseignant)
Headers: `Content-Type: multipart/form-data`
Body (multipart): `titre`, `description`, `classe_id`, `date_rendu`, `fichier`?
- 201: `{ "data": { "id": 1, ... } }`
- 422

### GET /api/devoirs/{id}
- 200: `{ "data": { ... } }`
- 404

### POST /api/devoirs/{id}/remises (élève)
Headers: `Content-Type: multipart/form-data`
Body: `fichier`
- 201

---

## Notes
### GET /api/notes?eleve_id=33&matiere=Maths
- 200:
```json
{ "data": [{ "id":1, "eleve_id":33, "matiere":"Maths", "note":15.5, "coef":1, "commentaire":"Bon travail" }] }
```

### POST /api/notes (enseignant)
Body:
```json
{ "eleve_id":33, "devoir_id":1, "matiere":"Maths", "note":15.5, "coef":1, "commentaire":"..." }
```
- 201
- 422

---

## Évènements
### GET /api/evenements?after=2025-09-01&before=2025-09-30
- 200: `{ "data": [ {"id":1, "titre":"Réunion", "date":"2025-09-05"} ] }`

### POST /api/evenements (admin/enseignant)
Body:
```json
{ "titre":"Réunion parents-profs", "description":"...", "date":"2025-09-05", "visible_pour":"parents" }
```
- 201

---

## Messages
### GET /api/messages?with=thread
- 200:
```json
{ "data": [ { "id":1, "sujet":"Info", "from": {"id":5,"name":"Prof"}, "to": {"id":9,"name":"Parent"}, "lu":false, "children":[...] } ] }
```

### POST /api/messages
Body:
```json
{ "sujet":"Absence", "contenu":"...", "to_user_id":9 }
```
- 201

### POST /api/messages/{id}/reply
Body:
```json
{ "contenu":"Merci pour l'info." }
```
- 201

### POST /api/messages/{id}/read
- 204

---

## Menus
### GET /api/menus?week=2025-W37
- 200: `{ "data": [ {"date":"2025-09-10", "entree":"...", "plat":"...", "dessert":"...", "allergenes": ["gluten"] } ] }`

### POST /api/menus (admin)
Body:
```json
{ "date":"2025-09-10", "entree":"...", "plat":"...", "dessert":"...", "allergenes":["gluten"] }
```
- 201

---

## Erreurs type
- 401 Unauthorized:
```json
{ "errors": [{ "code": "AUTH_401", "message": "Unauthenticated." }] }
```
- 403 Forbidden:
```json
{ "errors": [{ "code": "AUTH_403", "message": "You are not allowed to perform this action." }] }
```
- 404 Not Found:
```json
{ "errors": [{ "code": "GEN_404", "message": "Resource not found." }] }
```
- 422 Validation:
```json
{ "errors": [{ "code": "VAL_422", "message": "Validation failed.", "fields": { "email":["has already been taken"] } }] }
```

---

## Remarques d’implémentation
- Utiliser FormRequests pour chaque POST/PATCH.
- Sérialiser via API Resources.
- Policies/Middlewares par rôle.
- Rate limiting `api` middleware group.
- Uploads stockés dans `storage/app/public` + `php artisan storage:link`.
