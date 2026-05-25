<?php
$pageTitle = 'Dashboard';
function statusBadge(string $s): string {
    $m=['active'=>'green','verified'=>'green','approved'=>'green','resolved'=>'green','blocked'=>'red','pending'=>'yellow','open'=>'yellow','rejected'=>'gray'];
    $c=$m[strtolower($s)]??'blue';
    return "<span class='badge badge-{$c}'>".htmlspecialchars(ucfirst($s))."</span>";
}
?>

<div class="stats-grid">
<?php
$cards=[
  ['label'=>'Total Users',     'val'=>$stats['total_users'],        'icon'=>'ti-users',     'bg'=>'#e8f0fb','ic'=>'#0a66c2','link'=>APP_URL.'/users'],
  ['label'=>'Companies',       'val'=>$stats['total_companies'],    'icon'=>'ti-building',  'bg'=>'#e8f5e9','ic'=>'#057642','link'=>APP_URL.'/agents'],
  ['label'=>'Posts',           'val'=>$stats['total_posts'],        'icon'=>'ti-file-text', 'bg'=>'#f3e5f5','ic'=>'#7b1fa2','link'=>APP_URL.'/content'],
  ['label'=>'Jobs',            'val'=>$stats['total_jobs'],         'icon'=>'ti-briefcase', 'bg'=>'#fff8e1','ic'=>'#e65100','link'=>APP_URL.'/jobs'],
  ['label'=>'Applications',    'val'=>$stats['total_applications'], 'icon'=>'ti-send',      'bg'=>'#fbe9e7','ic'=>'#cc1016','link'=>APP_URL.'/jobs'],
  ['label'=>'Pending Reports', 'val'=>$stats['pending_reports'],    'icon'=>'ti-flag',      'bg'=>'#e8f0fb','ic'=>'#0a66c2','link'=>APP_URL.'/reports'],
];
foreach($cards as $c): ?>
<a href="<?=$c['link']?>" class="stat-card" style="text-decoration:none;color:inherit;cursor:pointer;display:flex;align-items:center;gap:14px;transition:box-shadow .18s,transform .18s;" onmouseover="this.style.boxShadow='0 4px 18px rgba(10,102,194,0.13)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='';this.style.transform=''">
  <div class="stat-icon-box" style="background:<?=$c['bg']?>">
    <i class="ti <?=$c['icon']?>" style="color:<?=$c['ic']?>;font-size:22px"></i>
  </div>
  <div>
    <div class="stat-num"><?=number_format((int)$c['val'])?></div>
    <div class="stat-lbl"><?=$c['label']?></div>
    <div class="stat-chg <?=$c['val']>0?'up':'down'?>">
      <i class="ti ti-<?=$c['val']>0?'trending-up':'minus'?>" style="font-size:12px"></i>
      <?=$c['val']>0?'Active':'None yet'?>
    </div>
  </div>
</a>
<?php endforeach; ?> 
</div>

<div class="chart-grid">
  <div class="chart-box">
    <div class="chart-title"><i class="ti ti-chart-bar" style="color:#0a66c2"></i> User Registrations (Last 6 Months)</div>
    <div class="chart-canvas-wrapper"><canvas id="userChart"></canvas></div>
  </div>
  <div class="chart-box">
    <div class="chart-title"><i class="ti ti-chart-line" style="color:#057642"></i> Job Applications (Last 6 Months)</div>
    <div class="chart-canvas-wrapper"><canvas id="appChart"></canvas></div>
  </div>
</div>

<div class="panel">
  <div class="panel-head">
    <span class="panel-title"><i class="ti ti-activity" style="color:#0a66c2"></i> Recent Activity</span>
    <span style="font-size:12px;color:#aaa">Latest admin actions</span>
  </div>
  <?php if(empty($activity)): ?>
    <div class="empty-state"><i class="ti ti-clipboard-list"></i><p>No activity yet.</p></div>
  <?php else: ?>
    <?php foreach($activity as $a): ?>
    <div class="act-item">
      <div class="act-ic"><i class="ti ti-info-circle"></i></div>
      <div style="flex:1">
        <div class="act-txt"><?=htmlspecialchars($a['description'])?></div>
        <div class="act-tm"><?=htmlspecialchars($a['created_at'])?></div>
      </div>
      <?php if(!empty($a['type'])): ?>
        <span class="badge badge-blue"><?=htmlspecialchars(ucfirst($a['type']))?></span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
window.addEventListener('load',function(){
  const labels=<?=json_encode($chart['labels'])?>;
  const uData=<?=json_encode($chart['users'])?>;
  const aData=<?=json_encode($chart['apps'])?>;
  new Chart(document.getElementById('userChart').getContext('2d'),{
    type:'bar',
    data:{labels,datasets:[{label:'New Users',data:uData,backgroundColor:'#0a66c2',borderRadius:6,borderSkipped:false}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#f0f0f0'},ticks:{font:{size:11}}},x:{grid:{display:false},ticks:{font:{size:11}}}}}
  });
  new Chart(document.getElementById('appChart').getContext('2d'),{
    type:'line',
    data:{labels,datasets:[{label:'Applications',data:aData,borderColor:'#057642',backgroundColor:'rgba(5,118,66,0.1)',tension:0.4,fill:true,pointRadius:5,pointBackgroundColor:'#057642',pointBorderColor:'#fff',pointBorderWidth:2}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#f0f0f0'},ticks:{font:{size:11}}},x:{grid:{display:false},ticks:{font:{size:11}}}}}
  });
});
</script>
