package com.example.myapplication.Navegation

import android.content.Context
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.adapter.PedidoAdapter
import com.example.myapplication.interfaces.ComidaAPI
import com.example.myapplication.models.Pedido
import com.google.gson.Gson
import okhttp3.OkHttpClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

class PedidosFragment : Fragment() {

    private lateinit var recyclerView: RecyclerView
    private var tvVacio: TextView? = null
    private lateinit var adapter: PedidoAdapter
    
    private var currentPage = 1
    private val limit = 10
    private var isLoading = false
    private var isLastPage = false

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_pedidos, container, false)

        recyclerView = view.findViewById(R.id.recycler_pedidos)
        tvVacio = view.findViewById(R.id.tvVacio)

        currentPage = 1
        isLoading = false
        isLastPage = false

        val layoutManager = LinearLayoutManager(requireContext())
        recyclerView.layoutManager = layoutManager
        
        adapter = PedidoAdapter(mutableListOf()) { pedido ->
            editarPedido(pedido)
        }
        recyclerView.adapter = adapter

        recyclerView.addOnScrollListener(object : RecyclerView.OnScrollListener() {
            override fun onScrolled(recyclerView: RecyclerView, dx: Int, dy: Int) {
                super.onScrolled(recyclerView, dx, dy)
                val visibleItemCount = layoutManager.childCount
                val totalItemCount = layoutManager.itemCount
                val firstVisibleItemPosition = layoutManager.findFirstVisibleItemPosition()

                if (!isLoading && !isLastPage) {
                    if ((visibleItemCount + firstVisibleItemPosition) >= totalItemCount
                        && firstVisibleItemPosition >= 0
                        && totalItemCount >= limit) {
                        cargarMasPedidos()
                    }
                }
            }
        })

        obtenerPedidos(true)
        return view
    }

    private fun editarPedido(pedido: Pedido) {
        val bundle = Bundle()
        val pedidoJson = Gson().toJson(pedido)
        bundle.putString("PEDIDO_EDITAR", pedidoJson)
        findNavController().navigate(R.id.crearPedidoFragment, bundle)
    }

    private fun cargarMasPedidos() {
        currentPage++
        obtenerPedidos(false)
    }

    private fun obtenerPedidos(esPrimeraCarga: Boolean) {
        if (isLoading) return
        isLoading = true

        val prefs = requireActivity().getSharedPreferences("AUTH", Context.MODE_PRIVATE)
        val userId = prefs.getInt("USER_ID", -1)

        val client = OkHttpClient.Builder().addInterceptor { chain ->
            val request = chain.request().newBuilder().addHeader("Accept", "application/json").build()
            chain.proceed(request)
        }.build()

        val retrofit = Retrofit.Builder()
            .baseUrl("http://10.0.2.2:8000/api/")
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        val api = retrofit.create(ComidaAPI::class.java)

        api.getPedidos(currentPage, limit, userId, "id", "desc").enqueue(object : Callback<List<Pedido>> {
            override fun onResponse(call: Call<List<Pedido>>, response: Response<List<Pedido>>) {
                isLoading = false
                if (response.isSuccessful) {
                    val listaRaw = response.body() ?: emptyList()
                    val lista = listaRaw.sortedByDescending { it.id }
                    
                    if (esPrimeraCarga) {
                        if (lista.isEmpty()) {
                            tvVacio?.visibility = View.VISIBLE
                            recyclerView.visibility = View.GONE
                        } else {
                            tvVacio?.visibility = View.GONE
                            recyclerView.visibility = View.VISIBLE
                            adapter = PedidoAdapter(lista.toMutableList()) { p -> editarPedido(p) }
                            recyclerView.adapter = adapter
                        }
                    } else {
                        if (lista.isEmpty()) isLastPage = true
                        else adapter.addItems(lista)
                    }
                    if (listaRaw.size < limit) isLastPage = true
                }
            }
            override fun onFailure(call: Call<List<Pedido>>, t: Throwable) {
                isLoading = false
            }
        })
    }
}
