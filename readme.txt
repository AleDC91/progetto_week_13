Per inizializzare l'app, inserire i parametri di connessione al db 
nel file settings/config.php
Verranno creati in automatico il database e la tabella users.
La tabella users è popolata in automatico con 7 utenti e un admin.
Gli utenti di default si possono settare prima dell'inizializzazione del db 
dal file settings/defaultUsers.php

Per loggarsi come admin le credenziali sono:
email: admin@admin.com
password: admin1234

La registrazione permette di iscirversi come utenti base, senza privilegi di admin.
Solo l'admin può eleggere gli utenti ad admin.
Ogni utente di tipo admin ha accesso al pannello riservato per eseguire operazioni CRUD.
Ogni admin può cancellare gli altri e mai se stesso, impedendo così di rimanere senza utenti di tipo admin.
In un app reale questo non avrebbe molto senso, servirebbe un superadmin.

La gestione degli utenti è stata fatta per mezzo di classi, anche dove non necessario
e qualche volta in modo logicamente forzato, al solo scopo di mettere in pratica 
i princìpi imparati a lezione.

I form di login e registrazione sono stati creati con la classe Form.
È stato inserito anche un sistema di log per registrare gli eventi ed eventuali errori dell'app.
la classe Logging è stata creata sfuttando il pattern singleton.
