package com.example.myapplication.models;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class Pedido {
    @SerializedName("id")
    private Integer id;

    private String fecha;
    private Float total;
    private List<PedidoComida> comidas;
    
    @SerializedName("usuario_nombre")
    private String usuarioNombre;

    @SerializedName("usuario_id")
    private Integer usuarioId;

    @SerializedName("n_mesas")
    private String nMesas;

    // Getters y Setters
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }
    
    public String getFecha() { return fecha; }
    public void setFecha(String fecha) { this.fecha = fecha; }
    
    public Float getTotal() { return total; }
    public void setTotal(Float total) { this.total = total; }
    
    public List<PedidoComida> getComidas() { return comidas; }
    public void setComidas(List<PedidoComida> comidas) { this.comidas = comidas; }

    public String getUsuarioNombre() { return usuarioNombre; }
    public void setUsuarioNombre(String usuarioNombre) { this.usuarioNombre = usuarioNombre; }

    public Integer getUsuarioId() { return usuarioId; }
    public void setUsuarioId(Integer usuarioId) { this.usuarioId = usuarioId; }

    public String getnMesas() { return nMesas; }
    public void setnMesas(String nMesas) { this.nMesas = nMesas; }
}
