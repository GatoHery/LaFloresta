package com.example.myapplication.models;

import com.google.gson.annotations.SerializedName;

public class Usuario {
    private Integer id;
    private String nombre;private String rol;

    @SerializedName("contrasena") // Para que coincida con tu DB/Node.js
    private String contrasena;

    public Usuario() {}

    // Constructor completo
    public Usuario(Integer id, String nombre, String rol, String contrasena) {
        this.id = id;
        this.nombre = nombre;
        this.rol = rol;
        this.contrasena = contrasena;
    }
    public String getContrasena() { return contrasena; }
    public void setContrasena(String contrasena) { this.contrasena = contrasena; }

    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }

    public String getNombre() { return nombre; }
    public void setNombre(String nombre) { this.nombre = nombre; }

    public String getRol() { return rol; }
    public void setRol(String rol) { this.rol = rol; }
}