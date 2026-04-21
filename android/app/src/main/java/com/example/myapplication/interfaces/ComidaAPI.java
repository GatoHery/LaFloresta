package com.example.myapplication.interfaces;

import com.example.myapplication.models.Comida;
import com.example.myapplication.models.Pedido;
import com.example.myapplication.models.Usuario;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.PUT;
import retrofit2.http.Path;
import retrofit2.http.Query;

public interface ComidaAPI {
    // Endpoints de Comida
    @GET("comidas")
    Call<List<Comida>> find(@Query("id") String id);
    
    @GET("comidas")
    Call<List<Comida>> getAll();

    // Endpoints de Pedidos
    @GET("pedidos")
    Call<List<Pedido>> getPedidos(
            @Query("_page") int page,
            @Query("_limit") int limit,
            @Query("usuario_id") Integer usuarioId,
            @Query("_sort") String sort,
            @Query("_order") String order
    );

    @POST("pedidos")
    Call<Pedido> createPedido(@Body Pedido pedido);

    @PUT("pedidos/{id}")
    Call<Pedido> updatePedido(@Path("id") int id, @Body Pedido pedido);

    // Endpoint de Login
    @GET("login")
    Call<Usuario> login(
            @Query("nombre") String nombre,
            @Query("contrasena") String contrasena
    );

    // Endpoints de Usuarios
    @GET("usuarios")
    Call<List<Usuario>> getUsuarios();

    @GET("usuarios/{id}")
    Call<Usuario> getUsuarioById(@Path("id") int id);
}
