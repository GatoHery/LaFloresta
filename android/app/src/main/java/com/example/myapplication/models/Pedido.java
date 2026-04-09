package com.example.myapplication.models;

import java.util.List;

public class Pedido {
    private Integer id;
    private String fecha;
    private Float total;
    private List<PedidoComida> comidas; // <--- Ahora es una lista

    // Getters y Setters
    public Integer getId() { return id; }
    public void setId(Integer id) { this.id = id; }
    public String getFecha() { return fecha; }
    public void setFecha(String fecha) { this.fecha = fecha; }
    public Float getTotal() { return total; }
    public void setTotal(Float total) { this.total = total; }
    public List<PedidoComida> getComidas() { return comidas; }
    public void setComidas(List<PedidoComida> comidas) { this.comidas = comidas; }
}