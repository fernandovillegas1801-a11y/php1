from flask import Flask, render_template, request, session
import random
import os
import mysql.connector
from urllib.parse import urlparse

def get_connection():
    db_url = os.getenv("MYSQL_PUBLIC_URL")

    print("URL:", db_url)

    if not db_url:
        raise Exception("MYSQL_PUBLIC_URL no existe")

    parsed = urlparse(db_url)

    print("HOST:", parsed.hostname)
    print("PORT:", parsed.port)
    print("USER:", parsed.username)
    print("DB:", parsed.path.lstrip("/"))

    return mysql.connector.connect(
        host=parsed.hostname,
        port=parsed.port,
        user=parsed.username,
        password=parsed.password,
        database=parsed.path.lstrip("/")
    )

app = Flask(__name__)
app.secret_key = "clave-super-secreta"  # Necesaria para manejar sesiones

@app.route("/", methods=["GET", "POST"])
def index():
    # Si no existe número en sesión, lo crea
    if "numero" not in session:
        session["numero"] = random.randint(1, 100)
    if "veces" not in session:
        session["veces"] = 0

    mensaje = ""
    
    if request.method == "POST":
        try:
            intento = int(request.form["intento"])
            numero = session["numero"]
            veces_actual = int(session.get("veces", 0))
            veces_actual += 1
            session["veces"] = veces_actual

            if intento < numero:
                mensaje = "El número es MAYOR."
            elif intento > numero:
                mensaje = "El número es MENOR."
            else:
                conn = get_connection()
                cursor = conn.cursor()

                sql = "INSERT INTO usuario(aciertos, fecha) VALUES (%s, NOW())"
                cursor.execute(sql, (session["veces"],))
                conn.commit()
                cursor.close()
                conn.close()
    
                nombre = request.args.get("nombre", "")
                mensaje = "¡Adivinaste! "+nombre+" lo lograste en " + str(session["veces"]) + " oportunidades. Se generó un nuevo número."
                
                session["numero"] = random.randint(1, 100)
                session["veces"] = 0
        
        except ValueError:
            mensaje = "Ingresa un número válido."

    return render_template("index.html", mensaje=mensaje)
