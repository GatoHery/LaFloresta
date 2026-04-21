package com.example.myapplication.models;

import com.google.gson.annotations.SerializedName;

public class PedidoComida {
    @SerializedName("comida_id")
    private Integer comidaId;
    
    @SerializedName("usuario_id")
    private Integer usuarioId;

    @SerializedName("usuario_nombre")
    private String usuarioNombre;

    private String nombre;
    private Float precio;
    private Integer cantidad;

    public PedidoComida() {}

    public Integer getComidaId() { return comidaId; }
    public void setComidaId(Integer comidaId) { this.comidaId = comidaId; }

    public Integer getUsuarioId() { return usuarioId; }
    public void setUsuarioId(Integer usuarioId) { this.usuarioId = usuarioId; }

    public String getUsuarioNombre() { return usuarioNombre; }
    public void setUsuarioNombre(String usuarioNombre) { this.usuarioNombre = usuarioNombre; }

    public String getNombre() { return nombre; }
    public void setNombre(String nombre) { this.nombre = nombre; }

    public Float getPrecio() { return precio; }
    public void setPrecio(Float precio) { this.precio = precio; }

    public Integer getCantidad() { return cantidad; }
    public void setCantidad(Integer cantidad) { this.cantidad = cantidad; }
}
