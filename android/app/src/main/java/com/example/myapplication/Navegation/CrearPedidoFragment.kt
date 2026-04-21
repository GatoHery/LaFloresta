package com.example.myapplication.Navegation

import android.content.Context
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.*
import androidx.fragment.app.Fragment
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.myapplication.R
import com.example.myapplication.adapter.ComidaAdapter
import com.example.myapplication.adapter.PedidoTemporalAdapter
import com.example.myapplication.interfaces.ComidaAPI
import com.example.myapplication.models.Comida
import com.example.myapplication.models.Pedido
import com.example.myapplication.models.PedidoComida
import com.google.android.material.bottomnavigation.BottomNavigationView
import com.google.gson.Gson
import okhttp3.OkHttpClient
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
    private lateinit var comidaAdapter: ComidaAdapter
    private lateinit var etNumeroMesa: EditText
    private lateinit var btnFinalizar: Button
    private var pedidoAEditar: Pedido? = null

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_crear_pedido, container, false)

        etNumeroMesa = view.findViewById(R.id.etNumeroMesa)
        btnFinalizar = view.findViewById(R.id.btnEnviarPedido)
        val spinnerCategorias = view.findViewById<Spinner>(R.id.spinnerCategorias)
        val rvComidasDisponibles = view.findViewById<RecyclerView>(R.id.rvComidasDisponibles)
        val rvItemsTemp = view.findViewById<RecyclerView>(R.id.rvItemsTemp)

        comidaAdapter = ComidaAdapter(emptyList()) { comida -> agregarAlPedido(comida, view) }
        rvComidasDisponibles.layoutManager = GridLayoutManager(requireContext(), 2)
        rvComidasDisponibles.adapter = comidaAdapter

        rvItemsTemp.layoutManager = LinearLayoutManager(requireContext())
        temporalAdapter = PedidoTemporalAdapter(listaTemporal) { posicion ->
            listaTemporal.removeAt(posicion)
            temporalAdapter.notifyDataSetChanged()
            actualizarCalculos(view)
        }
        rvItemsTemp.adapter = temporalAdapter

        cargarComidas(spinnerCategorias)

        arguments?.getString("PEDIDO_EDITAR")?.let { json ->
            pedidoAEditar = Gson().fromJson(json, Pedido::class.java)
            cargarDatosParaEditar(view)
        }

        spinnerCategorias.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, v: View?, position: Int, id: Long) {
                filtrarComidas(parent?.getItemAtPosition(position).toString())
            }
            override fun onNothingSelected(parent: AdapterView<*>?) {}
        }

        btnFinalizar.setOnClickListener {
            val mesa = etNumeroMesa.text.toString()
            if (mesa.isEmpty() || listaTemporal.isEmpty()) {
                Toast.makeText(requireContext(), "Faltan datos", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            procesarPedido(mesa)
        }

        return view
    }

    private fun cargarDatosParaEditar(view: View) {
        pedidoAEditar?.let { pedido ->
            etNumeroMesa.setText(pedido.getnMesas())
            btnFinalizar.text = "ACTUALIZAR PEDIDO"
            listaTemporal.clear()
            pedido.getComidas()?.let { items ->
                val prefs = requireActivity().getSharedPreferences("AUTH", Context.MODE_PRIVATE)
                val userId = prefs.getInt("USER_ID", -1)
                items.forEach { it.setUsuarioId(userId) }
                listaTemporal.addAll(items)
            }
            temporalAdapter.notifyDataSetChanged()
            actualizarCalculos(view)
        }
    }

    private fun agregarAlPedido(comida: Comida, view: View) {
        val prefs = requireActivity().getSharedPreferences("AUTH", Context.MODE_PRIVATE)
        val userId = prefs.getInt("USER_ID", -1)
        val existingItem = listaTemporal.find { it.comidaId == comida.id }
        if (existingItem != null) {
            existingItem.cantidad = (existingItem.cantidad ?: 0) + 1
            existingItem.setUsuarioId(userId)
        } else {
            listaTemporal.add(PedidoComida().apply {
                nombre = comida.nombre
                precio = comida.precio
                cantidad = 1
                comidaId = comida.id
                setUsuarioId(userId)
            })
        }
        temporalAdapter.notifyDataSetChanged()
        actualizarCalculos(view)
    }

    private fun filtrarComidas(categoria: String) {
        val listaFiltrada = if (categoria == "Todas") comidasDisponibles else comidasDisponibles.filter { it.tipo == categoria }
        comidaAdapter.updateList(listaFiltrada)
    }

    private fun cargarComidas(spinner: Spinner) {
        val client = OkHttpClient.Builder().addInterceptor { chain ->
            val request = chain.request().newBuilder().addHeader("Accept", "application/json").build()
            chain.proceed(request)
        }.build()

        val api = Retrofit.Builder()
            .baseUrl("http://10.0.2.2:8000/api/")
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ComidaAPI::class.java)

        api.getAll().enqueue(object : Callback<List<Comida>> {
            override fun onResponse(call: Call<List<Comida>>, response: Response<List<Comida>>) {
                if (response.isSuccessful) {
                    comidasDisponibles = response.body() ?: emptyList()
                    val categorias = mutableListOf("Todas").apply { addAll(comidasDisponibles.mapNotNull { it.tipo }.distinct()) }
                    if (isAdded) {
                        spinner.adapter = ArrayAdapter(requireContext(), android.R.layout.simple_spinner_item, categorias).apply { setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item) }
                        comidaAdapter.updateList(comidasDisponibles)
                    }
                }
            }
            override fun onFailure(call: Call<List<Comida>>, t: Throwable) {
                Log.e("API_ERROR", "Error: ${t.message}")
            }
        })
    }

    private fun procesarPedido(mesa: String) {
        val client = OkHttpClient.Builder().addInterceptor { chain ->
            val request = chain.request().newBuilder().addHeader("Accept", "application/json").build()
            chain.proceed(request)
        }.build()

        val api = Retrofit.Builder()
            .baseUrl("http://10.0.2.2:8000/api/")
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ComidaAPI::class.java)

        val prefs = requireActivity().getSharedPreferences("AUTH", Context.MODE_PRIVATE)
        val userId = prefs.getInt("USER_ID", -1)

        val pedido = (pedidoAEditar ?: Pedido()).apply {
            setTotal((subtotal * 1.10).toFloat())
            setComidas(listaTemporal)
            setUsuarioId(userId)
            setnMesas(mesa)
            if (pedidoAEditar == null) {
                setFecha(SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSS'Z'", Locale.US).apply { timeZone = TimeZone.getTimeZone("UTC") }.format(Date()))
            }
        }

        pedido.getComidas()?.forEach { it.setUsuarioId(userId) }

        val call = if (pedidoAEditar != null) api.updatePedido(pedido.getId(), pedido) else api.createPedido(pedido)

        call.enqueue(object : Callback<Pedido> {
            override fun onResponse(call: Call<Pedido>, response: Response<Pedido>) {
                if (response.isSuccessful) {
                    Toast.makeText(requireContext(), "¡Operación exitosa!", Toast.LENGTH_SHORT).show()
                    requireActivity().findViewById<BottomNavigationView>(R.id.bottom_navigation).selectedItemId = R.id.pedidosFragment
                } else {
                    val errorBody = response.errorBody()?.string()
                    Log.e("API_ERROR", "Status: ${response.code()} Body: $errorBody")
                    Toast.makeText(requireContext(), "Error del servidor: ${response.code()}", Toast.LENGTH_LONG).show()
                }
            }
            override fun onFailure(call: Call<Pedido>, t: Throwable) {
                Log.e("API_ERROR", "Fallo: ${t.message}")
            }
        })
    }

    private fun actualizarCalculos(view: View) {
        subtotal = listaTemporal.sumOf { ((it.precio ?: 0f) * (it.cantidad ?: 0)).toDouble() }
        val servicio = subtotal * 0.10
        view.findViewById<TextView>(R.id.tvSubtotal).text = "Subtotal: $${String.format("%.2f", subtotal)}"
        view.findViewById<TextView>(R.id.tvServicio).text = "Servicio (10%): $${String.format("%.2f", servicio)}"
        view.findViewById<TextView>(R.id.tvTotalFinal).text = "Total: $${String.format("%.2f", subtotal + servicio)}"
    }
}
