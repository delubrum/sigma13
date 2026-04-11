<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de Datos Personales</title>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f5f7fa;
            overflow: hidden;
        }
        
        /* Split Screen Layout */
        .split-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }
        
        .left-side {
            flex: 1;
            position: relative;
            background-image: url('app/assets/img/pd.png');
            background-size: cover;
            background-position: center;
        }
        
        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(19, 30, 52, 0.85), rgba(101, 43, 97, 0.75));
        }
        
        .right-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(245, 247, 250, 0.9);
            position: relative;
        }
        
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            width: 15px;
            height: 15px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            animation: float 6s linear infinite;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0) translateX(0) scale(1);
                opacity: 0;
            }
            5% {
                opacity: 0.9;
            }
            90% {
                opacity: 0.9;
            }
            100% {
                transform: translateY(-100vh) translateX(100px) scale(0);
                opacity: 0;
            }
        }
        
        .login-container {
            position: relative;
            width: 380px;
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
            z-index: 10;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: linear-gradient(90deg, 
                transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: translateX(-100%);
            transition: 1s;
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% {
                transform: translateX(-100%);
            }
            20%, 100% {
                transform: translateX(100%);
            }
        }
        
        .glass-effect {
            position: absolute;
            width: 150%;
            height: 150%;
            top: -25%;
            left: -25%;
            background: radial-gradient(
                circle at 50% 50%,
                rgba(255, 255, 255, 0.2),
                transparent 40%
            );
            pointer-events: none;
        }
        
        .company-logo {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        
        .company-logo h1 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 500;
            letter-spacing: 1px;
            margin-top: 15px;
            position: relative;
        }
        
        .logo-icon {
            width: 60%;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 0px;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }
        
        h2 {
            color: #2c3e50;
            font-size: 20px;
            font-weight: normal;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }
        
        h2::after {
            content: '';
            position: absolute;
            width: 50px;
            height: 2px;
            background: rgba(44, 62, 80, 0.3);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            transition: width 0.5s;
        }
        
        .login-container:hover h2::after {
            width: 100px;
        }
        
        .input-box {
            position: relative;
            width: 100%;
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .input-box::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            left: 0;
            background: rgba(44, 62, 80, 0.5);
            transform: translateX(-100%);
            transition: 0.5s;
        }
        
        .input-box:focus-within::before {
            transform: translateX(0);
        }
        
        .input-box input {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(44, 62, 80, 0.2);
            outline: none;
            border-radius: 5px;
            font-size: 14px;
            color: #2c3e50;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }
        
        .input-box input:focus {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .input-box input::placeholder {
            color: rgba(44, 62, 80, 0.7);
        }
        
        .btn {
            width: 100%;
            padding: 14px 0;
            background: rgba(44, 62, 80, 0.9);
            border: none;
            outline: none;
            border-radius: 5px;
            font-size: 14px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, rgba(255, 255, 255, 0.3), transparent);
            top: 0;
            left: -100%;
            transition: 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
            transition: 0.5s;
        }
        
        .btn:hover {
            background: rgb(44, 62, 80);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .footer-text {
            margin-top: 30px;
            text-align: center;
            color: rgba(44, 62, 80, 0.7);
            font-size: 12px;
        }
        
        .footer-links {
            margin-top: 15px;
            display: flex;
            justify-content: center;
        }
        
        .footer-links a {
            color: rgba(44, 62, 80, 0.8);
            text-decoration: none;
            font-size: 12px;
            margin: 0 10px;
            transition: 0.3s;
            position: relative;
        }
        
        .footer-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background: #2c3e50;
            transition: 0.3s;
        }
        
        .footer-links a:hover::after {
            width: 100%;
        }
        
        .footer-links a:hover {
            color: #2c3e50;
        }

        #loading {
            display: none;
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 20000;
            background: url(assets/img/loader.gif) center no-repeat #fff;
            background-size: 10vw;
            opacity: 0.9;
        }
        
        @keyframes formFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        form {
            animation: formFadeIn 1s ease-out forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .split-container {
                flex-direction: column;
                background-image: url('app/assets/img/pd.png');
                background-size: cover;
                background-position: center;
                position: relative;
            }
            
            .split-container::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, rgba(19, 30, 52, 0.85), rgba(43, 72, 101, 0.75));
                z-index: 1;
            }
            
            .left-side {
                display: none; /* Ocultar el lado izquierdo en móvil */
            }
            
            .right-side {
                height: 100vh;
                background-color: transparent;
                z-index: 2;
            }
            
            .login-container {
                width: 90%;
                max-width: 380px;
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <div class="left-side">
            <div class="particles">
                <script>
                    // Crear partículas dinámicas
                    for (let i = 0; i < 30; i++) {
                        const particle = document.createElement('div');
                        particle.classList.add('particle');
                        
                        // Posición aleatoria
                        const posX = Math.random() * 100;
                        const posY = Math.random() * 100 + 100;
                        const size = Math.random() * 15 + 8;
                        const duration = Math.random() * 7 + 4;
                        const delay = Math.random() * 2;
                        
                        particle.style.left = `${posX}%`;
                        particle.style.top = `${posY}%`;
                        particle.style.width = `${size}px`;
                        particle.style.height = `${size}px`;
                        particle.style.animationDuration = `${duration}s`;
                        particle.style.animationDelay = `${delay}s`;
                        
                        document.querySelector('.particles').appendChild(particle);
                    }
                </script>
            </div>
        </div>
        
        <div class="right-side">
            <div class="login-container">
                <div class="glass-effect"></div>
                <div class="company-logo">
                    <div class="logo-icon"><img src="app/assets/img/logoES.png" style="width:100%"></div>
                </div>
                <h2>Actualización de Datos Personales</h2>
                <form id ="test_login_form">
                    <div class="input-box">
                        <input type="text" name="user_id" placeholder="Número de identificación" required>
                    </div>
                    
                    <button type="submit" class="btn">Iniciar sesión</button>
                    
                    <!-- <div class="footer-text">
                        Sistema de acceso seguro
                    </div>
                    
                    <div class="footer-links">
                        <a href="#">Soporte técnico</a>
                        <a href="#">Política de acceso</a>
                    </div> -->
                </form>
            </div>
        </div>
    </div>

    <div id="loading"></div>
    
    <script>
    const notyf = new Notyf({
        duration: 3000,
        position: { x: "right", y: "top" }
    });

    document.getElementById("test_login_form").onsubmit = async (e) => {
        e.preventDefault();
        if (!e.target.reportValidity()) return;
        document.getElementById("loading").style.display = "block";
        try {
            const res = await fetch("?c=Employees&a=Login", {
                method: "POST",
                body: new FormData(e.target),
            });
            const data = await res.json();
            if (data.error) notyf.error(data.error);
        } catch {
            location.reload();
        } finally {
            document.getElementById("loading").style.display = "none";
        }
    };

    // Script para crear las partículas también en móvil
    window.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth <= 768) {
            const particlesDiv = document.createElement('div');
            particlesDiv.classList.add('particles');
            document.querySelector('.split-container').appendChild(particlesDiv);
            
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Posición aleatoria
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const size = Math.random() * 15 + 8;
                const duration = Math.random() * 7 + 4;
                const delay = Math.random() * 2;
                
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.animationDuration = `${duration}s`;
                particle.style.animationDelay = `${delay}s`;
                
                particlesDiv.appendChild(particle);
            }
        }
    });
    </script>
</body>
</html>