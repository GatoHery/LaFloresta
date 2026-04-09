package com.example.myapplication.models;

import com.google.gson.annotations.SerializedName;

public class PedidoComida {
    @SerializedName("comida_id")
    private Integer comidaId;
    private String nombre;
    private Float precio;
    private Integer cantidad;

    // Getters y Setters
    public Integer getComidaId() { return comidaId; }
    public void setComidaId(Integer comidaId) { this.comidaId = comidaId; }
    public String getNombre() { return nombre; }
    public void setNombre(String nombre) { this.nombre = nombre; }
    public Float getPrecio() { return precio; }
    public void setPrecio(Float precio) { this.precio = precio; }
    public Integer getCantidad() { return cantidad; }
    public void setCantidad(Integer cantidad) { this.cantidad = cantidad; }
}