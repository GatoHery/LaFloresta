package com.example.myapplication.Navegation

import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.adapterimport.PedidoTemporalAdapter
import com.example.myapplication.interfaces.ComidaAPI
import com.example.myapplication.models.Comida
import com.example.myapplication.models.Pedido
import com.example.myapplication.models.PedidoComida
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.text.SimpleDateFormat
import java.util.*

class CrearPedidoFragment : Fragment() {

    private var subtotal = 0.0
    private val listaTemporal = mutableListOf<PedidoComida>()
    private var comidasDisponibles = listOf<Comida>()
    private lateinit var temporalAdapter: PedidoTemporalAdapter

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_crear_pedido, container, false)

        val spinner = view.findViewById<Spinner>(R.id.spinnerComidas)
        val etCantidad = view.findViewById<EditText>(R.id.etCantidad)
        val btnAgregar = view.findViewById<Button>(R.id.btnAgregar)
        val rvItemsTemp = view.findViewById<RecyclerView>(R.id.rvItemsTemp)
        val btnFinalizar = view.findViewById<Button>(R.id.btnEnviarPedido)

        // Configurar el RecyclerView para la lista temporal (Carrito)
        rvItemsTemp.layoutManager = LinearLayoutManager(requireContext())
        temporalAdapter = PedidoTemporalAdapter(listaTemporal) { posicion ->
            listaTemporal.removeAt(posicion)
            temporalAdapter.notifyDataSetChanged()
            actualizarCalculos(view)
        }
        rvItemsTemp.adapter = temporalAdapter

        cargarComidas(spinner)

        // Lógica para añadir a la lista
        btnAgregar.setOnClickListener {
            val posicion = spinner.selectedItemPosition
            if (posicion >= 0 && comidasDisponibles.isNotEmpty()) {
                val comidaSeleccionada = comidasDisponibles[posicion]
                val cantidadStr = etCantidad.text.toString()
                val cant = if (cantidadStr.isNotEmpty()) cantidadStr.toInt() else 1

                val nuevoItem = PedidoComida().apply {
                    setNombre(comidaSeleccionada.nombre)
                    setPrecio(comidaSeleccionada.precio)
                    setCantidad(cant)
                    setComidaId(comidaSeleccionada.id)
                }

                listaTemporal.add(nuevoItem)
                temporalAdapter.notifyDataSetChanged()
                actualizarCalculos(view)
                etCantidad.text.clear()
            }
        }

        btnFinalizar.setOnClickListener {
            if (listaTemporal.isEmpty()) {
                Toast.makeText(requireContext(), "Agrega comida primero", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            enviarPedidoFinal()
        }

        return view
    }

    private fun enviarPedidoFinal() {
        val retrofit = Retrofit.Builder()
            .baseUrl("http://10.0.2.2:3000/")
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        val api = retrofit.create(ComidaAPI::class.java)

        val hoy = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date())
        val pedidoParaEnviar = Pedido().apply {
            setFecha(hoy)
            setTotal((subtotal * 1.10).toFloat())
            // PASO CLAVE: Usamos la lista temporal directamente, ya que contiene 'comida_id' y 'cantidad'
            setComidas(listaTemporal)
        }

        api.createPedido(pedidoParaEnviar).enqueue(object : Callback<Pedido> {
            override fun onResponse(call: Call<Pedido>, response: Response<Pedido>) {
                if (response.isSuccessful) {
                    Toast.makeText(requireContext(), "¡Pedido creado!", Toast.LENGTH_SHORT).show()
                    listaTemporal.clear()
                    findNavController().navigate(R.id.pedidosFragment)
                } else {
                    // Aquí verás por qué da error 500 si llegara a fallar
                    Log.e("API_ERROR", "Código: ${response.code()} Body: ${response.errorBody()?.string()}")
                }
            }
            override fun onFailure(call: Call<Pedido>, t: Throwable) {
                Log.e("API_ERROR", t.message.toString())
            }
        })
    }

    private fun cargarComidas(spinner: Spinner) {
        val retrofit = Retrofit.Builder()
            .baseUrl("http://10.0.2.2:3000/")
            .addConverterFactory(GsonConverterFactory.create())
            .build()

        val api = retrofit.create(ComidaAPI::class.java)
        api.getAll().enqueue(object : Callback<List<Comida>> {
            override fun onResponse(call: Call<List<Comida>>, response: Response<List<Comida>>) {
                if (response.isSuccessful) {
                    comidasDisponibles = response.body() ?: emptyList()
                    val nombres = comidasDisponibles.map { it.nombre }
                    val adapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, nombres)
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    spinner.adapter = adapter
                }
            }
            override fun onFailure(call: Call<List<Comida>>, t: Throwable) {
                Log.e("API_ERROR", t.message.toString())
            }
        })
    }

    private fun actualizarCalculos(view: View) {
        // Calculamos el subtotal: suma de (precio * cantidad) de cada item
        subtotal = listaTemporal.sumOf { ((it.precio ?: 0f) * (it.cantidad ?: 0)).toDouble() }
        val servicio = subtotal * 0.10
        val totalFinal = subtotal + servicio

        view.findViewById<TextView>(R.id.tvSubtotal).text = "Subtotal: $${String.format("%.2f", subtotal)}"
        view.findViewById<TextView>(R.id.tvServicio).text = "Servicio (10%): $${String.format("%.2f", servicio)}"
        view.findViewById<TextView>(R.id.tvTotalFinal).text = "Total: $${String.format("%.2f", totalFinal)}"
    }
}