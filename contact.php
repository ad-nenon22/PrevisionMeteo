<?php
declare(strict_types=1);
$page_title = "Contact Météo News";
$page_footer_info = "contact";
// Inclusion du header
require "include/header.inc.php";
?>



        <main>
<h1 class="page-title">Contactez-nous</h1>
            <p class="page-subtitle">Notre équipe est à votre disposition pour vous aider et répondre à vos questions</p>
        <div class="content-grid">
            <!-- Informations de contact -->
            <section class="info-block">
                <h2 class="block-heading">Nos coordonnées</h2>
                <p class="block-text">Nous sommes ravis de pouvoir vous aider. Utilisez ces informations pour nous contacter ou remplissez le formulaire ci-contre.</p>
                
                <ul class="info-list">
                    <li class="info-item">
                        <div class="info-icon icon-email"></div>
                        <div class="info-content">
                            <span class="info-label">Email</span>
                            <span class="info-value">contact@meteonews.fr</span>
                        </div>
                    </li>
                    
                    <li class="info-item">
                        <div class="info-icon icon-location"></div>
                        <div class="info-content">
                            <span class="info-label">Adresse</span>
                            <span class="info-value">1 avenue adolphe chauvin, 75001 Paris, France</span>
                        </div>
                    </li>
                    
                    <li class="info-item">
                        <div class="info-icon icon-phone"></div>
                        <div class="info-content">
                            <span class="info-label">Téléphone</span>
                            <span class="info-value">+33 0712345678</span>
                        </div>
                    </li>
                    
                    <li class="info-item">
                        <div class="info-icon icon-clock"></div>
                        <div class="info-content">
                            <span class="info-label">Horaires</span>
                            <span class="info-value">Lundi - Vendredi: 9h à 18h</span>
                        </div>
                    </li>
                </ul>
                
                <div class="social-block">
                    <h3 class="social-title">Retrouvez-nous sur les réseaux</h3>
                    <div class="social-links">
                        <a href="#" class="social-btn icon-twitter" aria-label="Twitter"></a>
                        <a href="#" class="social-btn icon-facebook" aria-label="Facebook"></a>
                        <a href="#" class="social-btn icon-instagram" aria-label="Instagram"></a>
                        <a href="#" class="social-btn icon-linkedin" aria-label="LinkedIn"></a>
                    </div>
                </div>
            </section>

            <!-- Formulaire de contact -->
            <section class="info-block">
                <h2 class="block-heading">Envoyez-nous un message</h2>
                <form class="contact-form">
                    <div class="form-group">
                        <label for="name" class="form-label">Nom complet</label>
                        <input type="text" id="name" class="form-input" placeholder="Votre nom" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" id="email" class="form-input" placeholder="votre.email@exemple.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">Sujet</label>
                        <select id="subject" class="form-select" required>
                            <option value="">Sélectionnez un sujet</option>
                            <option value="information">Demande d'information</option>
                            <option value="support">Support technique</option>
                            <option value="feedback">Commentaires et suggestions</option>
                            <option value="partnership">Partenariat</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" class="form-textarea" placeholder="Écrivez votre message ici..." required></textarea>
                    </div>
                    
                    <button type="submit" class="form-button">Envoyer le message</button>
                </form>
            </section>
        </div>

        <!-- Section FAQ -->
        <section class="faq-wrapper">
            <h2 class="faq-title">Questions fréquemment posées</h2>
            
            <div class="faq-container">
                <article class="faq-card">
                    <h3 class="faq-question">Comment consulter les prévisions météo pour ma ville ?</h3>
                    <p class="faq-answer">Utilisez simplement notre barre de recherche en haut de la page d'accueil pour entrer le nom de votre ville, ou utilisez la géolocalisation pour obtenir automatiquement les prévisions de votre position actuelle.</p>
                </article>
                
                <article class="faq-card">
                    <h3 class="faq-question">Quelle est la précision de vos prévisions météorologiques ?</h3>
                    <p class="faq-answer">Nos prévisions sont généralement précises à plus de 90% pour les 48 premières heures, et à environ 80% jusqu'à 7 jours. Nous utilisons des données provenant de multiples sources météorologiques professionnelles.</p>
                </article>
                
                <article class="faq-card">
                    <h3 class="faq-question">Est-il possible d'obtenir des alertes météo personnalisées ?</h3>
                    <p class="faq-answer">Oui, en créant un compte gratuit, vous pouvez configurer des alertes personnalisées pour recevoir des notifications en cas d'événements météorologiques importants dans les villes de votre choix.</p>
                </article>
                
                <article class="faq-card">
                    <h3 class="faq-question">Comment signaler une erreur dans les prévisions ?</h3>
                    <p class="faq-answer">Si vous constatez une inexactitude dans nos prévisions, utilisez notre formulaire de contact en sélectionnant "Support technique" comme sujet et précisez la ville et la date concernées.</p>
                </article>
            </div>
        </section>
    </div>



     <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .page-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        

        /* Grille principale */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Bloc d'informations */
        .info-block {
            background-color: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .block-heading {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }

        .block-text {
            margin-bottom: 25px;
            color: #555;
            line-height: 1.7;
        }

        /* Liste de contacts */
        .info-list {
            list-style: none;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background-color: #ebf5fb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            color: #3498db;
        }

        .info-content {
            flex-grow: 1;
        }

        .info-label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .info-value {
            display: block;
            color: #555;
        }

        /* Réseaux sociaux */
        .social-block {
            margin-top: 30px;
        }

        .social-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: transform 0.3s, background-color 0.3s;
        }

        .social-btn:hover {
            transform: translateY(-3px);
            background-color: #2980b9;
        }

        /* Formulaire */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            border-color: #3498db;
            outline: none;
        }

        .form-textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-button:hover {
            background-color: #2980b9;
        }

        /* FAQ */
        .faq-wrapper {
            margin-top: 60px;
        }

        .faq-title {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .faq-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 25px;
        }

        @media (max-width: 480px) {
            .faq-container {
                grid-template-columns: 1fr;
            }
        }

        .faq-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .faq-card:hover {
            transform: translateY(-5px);
        }

        .faq-question {
            font-size: 1.1rem;
            margin-bottom: 12px;
            color: #2c3e50;
        }

        .faq-answer {
            color: #555;
            line-height: 1.7;
        }

        /* Icônes (en utilisant caractères ou entités HTML comme alternative aux images) */
        .icon-email::before { content: "✉"; }
        .icon-location::before { content: "📍"; }
        .icon-phone::before { content: "📞"; }
        .icon-clock::before { content: "🕒"; }
        .icon-twitter::before { content: "𝕏"; }
        .icon-facebook::before { content: "f"; }
        .icon-instagram::before { content: "📸"; }
        .icon-linkedin::before { content: "in"; }
    </style>

</main>

<?php
    require "./include/footer.inc.php";
?>