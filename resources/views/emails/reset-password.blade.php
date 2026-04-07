<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #1f2937;">Restablecer tu contraseña</h2>

    <p style="color: #4b5563; line-height: 1.6;">
        ¿Olvidaste tu contraseña? No hay problema. Recibes este correo porque solicitaste un enlace para restablecer tu contraseña en SIGMA.
    </p>

    <p style="margin: 24px 0;">
        <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}"
           style="background-color: #171717; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
            Restablecer contraseña
        </a>
    </p>

    <p style="color: #6b7280; font-size: 14px;">
        Si no solicitaste este enlace puedes ignorarlo.
    </p>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

    <p style="color: #9ca3af; font-size: 12px;">
        Este enlace expirará en 60 minutos.
    </p>
</body>
</html>