@extends('layout.plantilla')

@section('title', 'Dashboard')
@section('active_nav', 'dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<main class="main-wrap">
    <!-- Tarjetas resumen con las metricas principales del dashboard. -->
    <div class="row g-3 mb-3">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <span class="badge-trend trend-neutral bt-abs" id="pct-animales">-</span>
                <div class="d-flex gap-2 mb-2">
                    <div class="card-icon" style="background:#fce7f3;">
                        <img src="{{ asset('images/cerdo.png') }}" alt="Porcino">
                    </div>
                    <div class="card-icon" style="background:#e0f2fe;">
                        <img src="{{ asset('images/vaca.png') }}" alt="Vacuno">
                    </div>
                    <div class="card-icon" style="background:#fef9c3;">
                        <img src="{{ asset('images/pollo.png') }}" alt="Avicola">
                    </div>
                </div>
                <div class="stat-value" id="total-animales"><span class="skeleton" style="width:80px;height:32px;">&nbsp;</span></div>
                <div class="stat-label">Total animales</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="card-icon mb-2" style="background:#f0fdf4;">
                    <i class="bi bi-building" aria-hidden="true"></i>
                </div>
                <div class="d-flex gap-4 mt-1">
                    <div class="sub-stat">
                        <div class="val" id="cebaderos-activos"><span class="skeleton" style="width:28px;height:20px;">&nbsp;</span></div>
                        <div class="text-muted" style="font-size:.72rem;">Activos</div>
                    </div>
                    <div class="sub-stat">
                        <div class="val" id="cebaderos-total" style="color:var(--green);"><span class="skeleton" style="width:28px;height:20px;">&nbsp;</span></div>
                        <div class="text-muted" style="font-size:.72rem;">Total</div>
                    </div>
                </div>
                <div class="stat-label mt-1">Cebaderos</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <div class="card-icon mb-2" style="background:#fef3c7;">
                    <i class="bi bi-basket-fill" aria-hidden="true"></i>
                </div>
                <div id="pienso-slides"><span class="skeleton" style="width:80px;height:32px;">&nbsp;</span></div>
                <div class="pienso-dots" id="pienso-dots"></div>
                <div class="stat-label">Pienso promedio</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card">
                <span class="badge-trend trend-neutral bt-abs" id="pct-tratamientos">-</span>
                <div class="card-icon mb-2" style="background:#fce7f3;">
                    <span class="card-emoji" aria-hidden="true">💉</span>
                </div>
                <div class="stat-value" id="tratamientos-activos"><span class="skeleton" style="width:60px;height:32px;">&nbsp;</span></div>
                <div class="stat-label">Tratamientos este mes</div>
            </div>
        </div>
    </div>

    <!-- Bloque central: listado rapido de animales y actividad reciente. -->
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="animals-card h-100">
                <div class="card-header-custom">
                    <div class="section-title mb-0">Animales recientes</div>
                    <a href="{{ route('animal.index') }}" class="dashboard-btn dashboard-btn-secondary dashboard-btn-sm">
                        Ver todos <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Cebadero</th>
                            <th>Lote</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-animales">
                        <tr><td colspan="4" class="text-center py-3 text-muted" style="font-size:.85rem;">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="activity-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="section-title mb-0">Actividad reciente</div>
                    <a href="#" class="dashboard-btn dashboard-btn-secondary dashboard-btn-sm">
                        Ver más <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div id="actividad-feed">
                    <div class="text-center py-3 text-muted" style="font-size:.85rem;">Cargando...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafico comparativo por cebadero con cambio de metrica desde botones. -->
    <div class="row g-3">
        <div class="col-12">
            <div class="chart-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="section-title mb-0">Estadisticas por cebadero</div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="dashboard-btn dashboard-btn-toggle active" id="btn-animales-chart" type="button">Animales</button>
                        <button class="dashboard-btn dashboard-btn-toggle" id="btn-pienso-chart" type="button">Pienso (T)</button>
                    </div>
                </div>
                <canvas id="cebaderoChart" height="80"></canvas>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// Centralizamos las rutas del dashboard aqui para no repetir strings en cada fetch.
const ROUTES = {
    stats: '{{ route("dashboard.stats") }}',
    animales: '{{ route("dashboard.animales-recientes") }}',
    actividad: '{{ route("dashboard.actividad-reciente") }}',
    cebaderos: '{{ route("dashboard.estadisticas-cebadero") }}',
};
const IMGS = {
    cerdo: '{{ asset("images/cerdo.png") }}',
    vaca: '{{ asset("images/vaca.png") }}',
    pollo: '{{ asset("images/pollo.png") }}',
};

function iconoEspecie(e){return{Porcino:IMGS.cerdo,Vacuno:IMGS.vaca,Avicola:IMGS.pollo,Avícola:IMGS.pollo}[e]??IMGS.vaca;}
// Aplica la clase visual del porcentaje segun suba, baje o se mantenga.
function badgeTrend(pct,id){
    if(pct===null||pct===undefined) return;
    const el=document.getElementById(id);
    const cls=pct>0?'trend-up':pct<0?'trend-down':'trend-neutral';
    el.className='badge-trend bt-abs '+cls;
    el.textContent=(pct>0?'+':'')+pct+'%';
}
// El backend ya resume el estado y aqui solo lo convertimos en badge visual.
function badgeEstado(e){
    const m={Saludable:'estado-saludable',Revision:'estado-revision',Critico:'estado-critico'};
    return `<span class="badge-estado ${m[e]??'estado-revision'}">${e}</span>`;
}
// Cada tipo de evento del feed usa su propio icono, color y etiqueta.
function iconoActividad(tipo){
    return{
        alimentacion:{img:IMGS.pollo,bg:'#fef3c7',label:'Alimentacion registrada'},
        tratamiento:{img:IMGS.cerdo,bg:'#fce7f3',label:'Tratamiento registrado'},
        revision:{img:IMGS.vaca,bg:'#f0fdf4',label:'Revision registrada'},
    }[tipo]??{img:IMGS.pollo,bg:'#f0fdf4',label:'Actividad registrada'};
}

// Rellena las tarjetas superiores y monta el slider de pienso promedio.
async function loadStats(){
    const d=await fetch(ROUTES.stats).then(r=>r.json());
    document.getElementById('total-animales').textContent=d.total_animales.toLocaleString('es-ES');
    document.getElementById('cebaderos-activos').textContent=d.cebaderos_activos;
    document.getElementById('cebaderos-total').textContent=d.total_cebaderos;
    document.getElementById('tratamientos-activos').textContent=d.tratamientos_activos;
    badgeTrend(d.pct_animales,'pct-animales');
    badgeTrend(d.pct_tratamientos,'pct-tratamientos');

    const slides=document.getElementById('pienso-slides');
    const dotsEl=document.getElementById('pienso-dots');
    slides.innerHTML=''; dotsEl.innerHTML='';
    if(!d.pienso_especies.length){
        // Si no hay datos de alimentacion, dejamos un valor neutro.
        slides.innerHTML='<div class="stat-value">-</div>';
        return;
    }
    d.pienso_especies.forEach((item,i)=>{
        const div=document.createElement('div');
        div.className='pienso-slide'+(i===0?' active':'');
        div.innerHTML=`<div class="stat-value">${item.unidad}</div><div style="font-size:.72rem;color:var(--muted);">${item.especie}</div>`;
        slides.appendChild(div);
        const dot=document.createElement('div');
        dot.className='pienso-dot'+(i===0?' active':'');
        dot.addEventListener('click',()=>goSlide(i));
        dotsEl.appendChild(dot);
    });
    let cur=0;
    // El slider solo alterna la clase active entre tarjetas y dots.
    function goSlide(idx){
        cur=idx;
        document.querySelectorAll('.pienso-slide').forEach((s,i)=>s.classList.toggle('active',i===idx));
        document.querySelectorAll('.pienso-dot').forEach((d,i)=>d.classList.toggle('active',i===idx));
    }
    setInterval(()=>goSlide((cur+1)%d.pienso_especies.length),3000);
}

// Carga la tabla de animales recientes desde la API del dashboard.
async function loadAnimales(){
    const data=await fetch(ROUTES.animales).then(r=>r.json());
    const tbody=document.getElementById('tabla-animales');
    tbody.innerHTML='';
    if(!data.length){tbody.innerHTML='<tr><td colspan="4" class="text-center py-3 text-muted">Sin registros</td></tr>';return;}
    data.forEach(a=>{
        const tr=document.createElement('tr');
        tr.innerHTML=`<td><img src="${iconoEspecie(a.especie)}" class="animal-icon" alt="${a.especie}">${a.codigo}</td>
                      <td>${a.cebadero_nombre}</td>
                      <td>${a.lote??'-'}</td>
                      <td>${badgeEstado(a.estado)}</td>`;
        tbody.appendChild(tr);
    });
}

// Construye el feed de actividad mezclando revisiones, tratamientos y alimentacion.
async function loadActividad(){
    const data=await fetch(ROUTES.actividad).then(r=>r.json());
    const feed=document.getElementById('actividad-feed');
    feed.innerHTML='';
    if(!data.length){feed.innerHTML='<div class="text-center py-3 text-muted" style="font-size:.85rem;">Sin actividad</div>';return;}
    data.forEach(item=>{
        const info=iconoActividad(item.tipo);
        const div=document.createElement('div');
        div.className='activity-item';
        div.innerHTML=`<div class="act-icon" style="background:${info.bg};"><img src="${info.img}" alt="${item.tipo}"></div>
                       <div><div class="act-text">${info.label}</div><div class="act-time">${item.fecha} ? ${item.codigo}</div></div>`;
        feed.appendChild(div);
    });
}

let chart,chartData={labels:[],animales:[],pienso:[]};
// Preparamos el grafico una sola vez y guardamos ambas series para alternar entre ellas.
async function loadChart(){
    const data=await fetch(ROUTES.cebaderos).then(r=>r.json());
    chartData.labels=data.map(c=>c.nombre);
    chartData.animales=data.map(c=>c.total_animales);
    chartData.pienso=data.map(c=>parseFloat(c.pienso_promedio_t));
    const ctx=document.getElementById('cebaderoChart').getContext('2d');
    chart=new Chart(ctx,{
        type:'bar',
        data:{labels:chartData.labels,datasets:[{label:'Animales',data:chartData.animales,backgroundColor:'#2ecc7120',borderColor:'#2ecc71',borderWidth:2,borderRadius:8}]},
        options:{responsive:true,plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{grid:{color:'#f3f4f6'},beginAtZero:true}}}
    });
}
// Estos botones no vuelven a pedir datos: solo cambian la serie ya cargada.
document.getElementById('btn-animales-chart').addEventListener('click',function(){
    chart.data.datasets[0].data=chartData.animales;chart.data.datasets[0].label='Animales';chart.update();
    this.classList.add('active');document.getElementById('btn-pienso-chart').classList.remove('active');
});
document.getElementById('btn-pienso-chart').addEventListener('click',function(){
    chart.data.datasets[0].data=chartData.pienso;chart.data.datasets[0].label='Pienso (T)';chart.update();
    this.classList.add('active');document.getElementById('btn-animales-chart').classList.remove('active');
});

// Lanzamos todas las cargas a la vez para que el dashboard se complete mas rapido.
Promise.all([loadStats(),loadAnimales(),loadActividad(),loadChart()]);
</script>
@endpush

