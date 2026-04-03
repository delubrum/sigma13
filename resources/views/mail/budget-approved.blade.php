<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Presupuesto Aprobado</title>
</head>
<body style="font-family: system-ui, sans-serif; background: #1a1a1a; color: #fff; padding: 40px;">
    <div style="max-width: 600px; margin: 0 auto; background: #262626; border-radius: 12px; padding: 32px;">
        <h1 style="color: #22c55e; margin-bottom: 24px;">✅ Presupuesto Aprobado</h1>
        
        <p style="color: #a1a1aa; margin-bottom: 16px;">Su presupuesto ha sido aprobado exitosamente.</p>
        
        <div style="background: #171717; border-radius: 8px; padding: 20px; margin: 24px 0;">
            <p style="margin: 8px 0;"><strong style="color: #fff;">Presupuesto:</strong> <span style="color: #22c55e;">{{ $budgetNumber }}</span></p>
            <p style="margin: 8px 0;"><strong style="color: #fff;">Importe:</strong> <span style="color: #22c55e;">${{ number_format($amount, 2) }}</span></p>
        </div>
        
        <p style="color: #71717a; font-size: 14px;">Este es un correo automático de SIGMA - Sistema de Gestión.</p>
    </div>
</body>
</html>