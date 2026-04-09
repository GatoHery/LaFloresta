package com.example.myapplication.interfaces;

import com.example.myapplication.models.Comida;
import com.example.myapplication.models.Pedido;
import java.util.List;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Query;

public interface ComidaAPI {
    @GET("/comida")
    public Call<List<Comida>> find(@Query("id") String id);
    
    @GET("/comida")
    public Call<List<Comida>> getAll();

    @GET("/pedido")
    public Call<List<Pedido>> getPedidos();

    @POST("/pedido")
    public Call<Pedido> createPedido(@Body Pedido pedido);
}
