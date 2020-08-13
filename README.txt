Tom Albrecht, Matrikelnummer: 5043317
Nick Garbusa, Matrikelnummer: 4551767
Andree Hildebrandt, Matrikelnummer: 4394647

Voraussetzungen:
Benutzer:
Name: nick, Passwort: nick


Funktionalit�ten:
- Footer:
   - Navigation (wenn nicht eingeloggt):
	- Datenschutz, Impressum, Sitemap, Index, Blog, Map, Login, Register
   - Navigation (wenn eingeloggt):
	- Datenschutz, Impressum, Sitemap, Index, Blog, Map, Beitrag erstellen, Logout
   - aktive Seite wird hervorgehoben
   - Copyright Hinweis f�r die Daten

- Header:
   - Navigation (wenn nicht eingeloggt):
	- Index, Blog, Map, Kontakt, Login, Register
   - Navigation (wenn eingeloggt):
	- Index, Blog, Map, Kontakt, Beitrag erstellen, Logount
   - aktive Seite wird hervorgehoben

- Index:
   - angezeigte Beitr�ge
	- mit Titel, Bild, Beschreibung (gek�rzt), Grober Standort, Durchschnittlicher Bewertung, Tags (als Bilder)
	- mit Link zum Beitrag
        - wenn Beitrag kein Bild enth�lt, wird ein Dummybild angezeigt

- Blog:
  - als Header Beitrag wird immer beliebtester See angezeigt
  - Suchfunktion
	- nach Seename
 	- filtern in den Suchergebnisse
	- Suche per AJAX Request
  - Filter
	- nach Badesee, Angelsee, Hundestrand, WC/Duschen, Grillen erlaubt, WLAN
  - angezeigte Beitr�ge
	- mit Titel, Bild, Beschreibung (gek�rzt), Grober Standort, Durchschnittlicher Bewertung, Tags (als Bilder)
	- mit Link zum Beitrag
	- wenn Beitrag kein Bild enth�lt, wird ein Dummybild angezeigt

Beitrag_erstellen
   - Map-Picker f�llt Koordinaten Feld eigenst�ndig aus.
   - Beschreibungsfeld hat JS-Validierung ob mindestens 50 Zeichen erreicht wurden und gibt Feedback dar�ber

Beitrag_detail
   - Bilder werden in Galerie angezeigt und werden per Mausklick in extra Vorschau ge�ffnet
   - Kommentar & Bewertungsfunktion
   - Link um Beitrag auf Karte anzuzeigen (um Datenvolumen zu sparen nicht auf dieser Seite selbst)
   - Sidebar in denen die 5 Top best bewerteten Beitr�ge angezeigt werden (verlinkt)
   - "Edit Article" Button (nur sichbar wenn der eingeloggte Benutzer Autor des Beitrags ist)

Beitrag_bearbeiten
   - nur aufrufbar, wenn der eingeloggte Benutzer Autor des Eintrags ist.
   - M�glichkeit alle Informationen des Sees zu ver�ndern
   - M�gichkeit den Beitrag zu l�schen
   - Bilder werden per Javascript markiert und nach Klick auf "�bernehmen" gel�scht

Registrieren
   - Password JS-Validierung um ein sicheres Password zu garantieren.

Login
-Login
  - Beitrag erstellen
  - Beitrag bearbeiten
  - Beitrag l�schen

Rechtliches
- Datenschutz, Impressum
- Disclaimer
	- Checkbox f�r Registrierung und Kontaktformular
	- Externe Links sind gekennzeichnet
- Cookie Pop-Up

SEO/Sicherheit
- dynamische Sitemap
- immer htmlspecialchars & PDO Statements verwendet
- Bei Bilderupload wird überprüft ob dies ein Bild ist
- Filesystem per Browser nicht zugreifbar
- Error/Warnings/Notices werden nicht angezeigt

Kontaktformular
- Kontaktnachricht wird per SMTP versendet
- Falls SMTP Authentifikation fehl schlägt, wird Errormeldung angezeigt
