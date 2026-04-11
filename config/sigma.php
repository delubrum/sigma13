<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SIGMA AI Configuration
    |--------------------------------------------------------------------------
    */

    'ai' => [
        'noise_words' => [
            // --- Técnicas / Mantenimiento / Soporte (Acciones y Contexto) ---
            'ajustar', 'ajuste', 'apoyo', 'arreglar', 'avería', 'cambio', 
            'checar', 'chequeo', 'cliente', 'comenta', 'configurar', 'configuración', 
            'daño', 'dañado', 'detalle', 'detectó', 'dispositivo', 'ejecuta', 
            'ejecutar', 'equipo', 'error', 'estado', 'evaluar', 'falla', 
            'falló', 'funciona', 'funcionando', 'genera', 'hace', 'incidencia', 
            'indica', 'inspección', 'instalar', 'instalación', 'limpiar', 'limpieza', 
            'mantenimiento', 'máquina', 'necesita', 'operativo', 'parte', 'pedido', 
            'pendiente', 'presenta', 'problema', 'procede', 'prueba', 'realiza', 
            'realizar', 'realizó', 'reemplazar', 'reemplazo', 'reparar', 'reporta', 
            'reporte', 'requiere', 'revisar', 'revisando', 'revisión', 'roto', 
            'servicio', 'sistema', 'solicita', 'solicitud', 'solucionar', 'solucionado', 
            'solución', 'tarea', 'ticket', 'trabajo', 'técnico', 'unidad', 
            'usuario', 'verificar', 'verificando', 'visita',

            // --- Verbos Auxiliares y Comunes (Conjugaciones) ---
            'da', 'dan', 'dar', 'decir', 'dice', 'dicen', 'dijo', 'dio',
            'era', 'eran', 'es', 'estamos', 'están', 'estar', 'estas', 'estoy', 'está', 
            'fue', 'fueron', 'fui', 'ha', 'hacer', 'hacen', 'haciendo', 'han', 
            'hay', 'he', 'hecho', 'hizo', 'hubo', 'ir', 'poder', 'puede', 
            'pueden', 'quiere', 'saber', 'ser', 'tener', 'tiene', 'tienen', 
            'tuve', 'tuvo', 'va', 'van', 'voy',

            // --- Conectores, Artículos, Preposiciones y Conjunciones ---
            'a', 'al', 'ante', 'bajo', 'cabe', 'con', 'contra', 'de', 
            'del', 'desde', 'durante', 'e', 'el', 'en', 'entre', 'hacia', 
            'hasta', 'la', 'las', 'lo', 'los', 'mediante', 'ni', 'o', 
            'para', 'pero', 'por', 'porque', 'pues', 'que', 'qué', 'según', 
            'si', 'sin', 'sino', 'sobre', 'tras', 'u', 'un', 'una', 
            'unas', 'unos', 'vía', 'y',

            // --- Adverbios, Cantidad y Temporalidad ---
            'actualmente', 'además', 'ahora', 'algo', 'antes', 'así', 'aún', 
            'ayer', 'bien', 'casi', 'como', 'cómo', 'cuando', 'cuándo', 
            'después', 'donde', 'dónde', 'entonces', 'finalmente', 'hoy', 'luego', 
            'mal', 'mientras', 'mucho', 'muy', 'más', 'menos', 'nada', 
            'no', 'nunca', 'poco', 'posible', 'probablemente', 'pronto', 'siempre', 
            'solo', 'solamente', 'también', 'tampoco', 'tarde', 'temprano', 'todavía', 
            'ya',

            // --- Pronombres y Determinantes ---
            'alguien', 'alguna', 'algunas', 'alguno', 'algunos', 'aquel', 'aquella', 
            'aquellas', 'aquellos', 'cada', 'conmigo', 'contigo', 'cual', 'cuales', 
            'cuál', 'cuáles', 'cualquier', 'cualquiera', 'cuya', 'cuyas', 'cuyo', 
            'cuyos', 'ella', 'ellas', 'ellos', 'esa', 'esas', 'ese', 
            'eso', 'esos', 'esta', 'estas', 'este', 'esto', 'estos', 
            'le', 'les', 'me', 'mi', 'mis', 'misma', 'mismas', 
            'mismo', 'mismos', 'mí', 'nadie', 'ninguna', 'ninguno', 'nos', 
            'nosotros', 'nuestra', 'nuestras', 'nuestro', 'nuestros', 'os', 'otra', 
            'otras', 'otro', 'otros', 'quien', 'quienes', 'quién', 'quiénes', 
            'se', 'su', 'sus', 'suya', 'suyas', 'suyo', 'suyos', 
            'te', 'ti', 'toda', 'todas', 'todo', 'todos', 'tu', 
            'tus', 'él', 'yo'
        ],
    ],
];