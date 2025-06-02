<?php
require_once $abs_us_root . $us_url_root . 'usersc/templates/' . $settings->template . '/container_close.php';
require_once $abs_us_root . $us_url_root . 'users/includes/page_footer.php';
?>

<style>
/* FOOTER PERSONALIZADO MEJORADO */
.custom-footer {
    background: #212529;
    color: #ecf0f1;
    padding: 40px 0 20px;
    margin-top: auto;
    border-top: 4px solid #e67e22;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid #34495e;
}

.footer-col h4 {
    color: #e67e22;
    margin-bottom: 20px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    position: relative;
    padding-bottom: 10px;
}

.footer-col h4:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background: #e67e22;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.contact-icon {
    background: rgba(230, 126, 34, 0.2);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: #e67e22;
    font-size: 16px;
    flex-shrink: 0;
}

.contact-text {
    font-size: 15px;
    color: #bdc3c7;
}

.quick-links {
    display: flex;
    flex-direction: column;
}

.quick-link {
    display: flex;
    align-items: center;
    color: #bdc3c7;
    text-decoration: none;
    padding: 8px 0;
    transition: all 0.3s ease;
}

.quick-link:hover {
    color: #e67e22;
    transform: translateX(5px);
    text-decoration: none;
}

.quick-link i {
    margin-right: 10px;
    font-size: 14px;
}

.social-links {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 20px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: #34495e;
    border-radius: 50%;
    color: #ecf0f1;
    font-size: 18px;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: #e67e22;
    color: white;
    transform: translateY(-3px);
    text-decoration: none;
}

.footer-bottom {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding-top: 20px;
    text-align: center;
}

.copyright {
    font-size: 14px;
    color: #bdc3c7;
    margin-bottom: 15px;
}

.legal-links {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

.legal-link {
    color: #bdc3c7;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
    display: flex;
    align-items: center;
}

.legal-link:hover {
    color: #e67e22;
    text-decoration: none;
}

.legal-link i {
    margin-right: 5px;
}

.footer-brand {
    margin-bottom: 20px;
    color: #e67e22;
    font-weight: 700;
    font-size: 24px;
    letter-spacing: 1px;
}

/* Responsive */
@media (max-width: 768px) {
    .footer-grid {
        grid-template-columns: 1fr;
    }
    
    .footer-col h4:after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .footer-col h4 {
        text-align: center;
    }
    
    .contact-item {
        justify-content: center;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .quick-links {
        align-items: center;
    }
    
    .quick-link:hover {
        transform: translateX(0) scale(1.05);
    }
}

/* Footer Mobile - Ultra simplificado */
@media (max-width: 480px) {
    .custom-footer {
        padding: 30px 0 15px !important;
    }
    
    .footer-grid {
        gap: 25px !important;
        padding-bottom: 20px;
    }
    
    .contact-icon {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
    
    .contact-text {
        font-size: 14px;
    }
    
    .footer-brand {
        font-size: 20px !important;
    }
    
    .copyright {
        font-size: 12px;
    }
    
    .legal-link {
        font-size: 12px;
    }
}
</style>
<div style="height: 20rem;"></div>
<footer class="custom-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Columna 1 - Información de Contacto -->
            <div class="footer-col">
                <h4>Contacto</h4>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="contact-text">
                        info@candeivid.com
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <div class="contact-text">
                        <a href="https://www.instagram.com/rutascandeivid" target="_blank" rel="noopener" style="color: #bdc3c7; text-decoration: none; transition: color 0.3s;">
                            @rutascandeivid
                        </a>
                    </div>
                </div>
                <!-- <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/rutascandeivid" target="_blank" rel="noopener" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                </div> -->
            </div>
            
            <!-- Columna 2 - Enlaces Rápidos -->
            <div class="footer-col">
                <h4>Enlaces Rápidos</h4>
                <div class="d-flex flex-row bd-highlight mb-3">
                    <a href="<?=$us_url_root?>./index.php" class="quick-link me-3">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                    <a href="<?=$us_url_root?>./pages/rutas.php" class="quick-link me-3">
                        <i class="fas fa-route"></i> Rutas
                    </a>
                    <a href="<?=$us_url_root?>./pages/contacto.php" class="quick-link me-3">
                        <i class="fas fa-envelope"></i> Contacto
                    </a>
                    <a href="<?=$us_url_root?>./pages/faq.php" class="quick-link me-3">
                        <i class="fas fa-question-circle"></i> FAQ
                    </a>
                </div>
            </div>
            
            <!-- Columna 3 - Sobre Candeivid -->
            <div class="footer-col">
                <h4>Sobre Candeivid</h4>
                <p class="mb-3" style="color: #bdc3c7; line-height: 1.6;">
                    Candeivid ofrece las mejores rutas motociclistas, cuidadosamente planificadas por expertos para garantizar la mejor experiencia de conducción.
                </p>
                <!-- <div class="footer-brand">
                    <i class="fas fa-motorcycle"></i> CANDEIVID
                </div> -->
            </div>
        </div>
        
        <!-- Copyright y Enlaces Legales -->
        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?php echo date('Y'); ?> Candeivid. Todos los derechos reservados
            </div>
            <div class="legal-links">
                <a href='<?=$us_url_root?>./pages/privacidad.php' class="legal-link">
                    <i class="fas fa-shield-alt"></i> Privacidad
                </a>
                <a href='<?=$us_url_root?>./pages/cookies.php' class="legal-link">
                    <i class="fas fa-cookie-bite"></i> Cookies
                </a>
                <a href='<?=$us_url_root?>./pages/terminos.php' class="legal-link">
                    <i class="fas fa-gavel"></i> Términos de Uso
                </a>
                <a href='<?=$us_url_root?>./pages/eliminar_usuario.php' class="legal-link">
                    <i class="fas fa-gavel"></i> Eliminar Usuario
                </a>
            </div>
        </div>
    </div>
</footer>

</body>

<?php require_once($abs_us_root.$us_url_root.'users/includes/html_footer.php');?>