from flask import Flask, render_template, request, session
import random
import os
import mysql.connector

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
                mysql.connector.connect(
                    host=os.environ.get("nozomi.proxy.rlwy.net"),
                    port=os.environ.get("51450"),
                    user=os.environ.get("root"),
                    password=os.environ.get("AjzweXbzIUGmSEtnbznPQdSBOJcLTbGX"),
                    database=os.environ.get("railway")


                conn = get_connection()
                cursor = conn.cursor()

                sql = "INSERT INTO usuarios(aciertos, fecha) VALUES (%s, NOW())"
                cursor.execute(sql, (intento,))
                conn.commit()

    
                nombre = request.args.get("nombre", "")
                mensaje = "¡Adivinaste! "+nombre+" lo lograste en " + str(session["veces"]) + " oportunidades. Se generó un nuevo número."
                
                session["numero"] = random.randint(1, 100)
                session["veces"] = 0
        
        except ValueError:
            mensaje = "Ingresa un número válido."

    return render_template("index.html", mensaje=mensaje)
