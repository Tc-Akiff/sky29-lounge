<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Tempahan Lounge SKY29</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-firestore-compat.js"></script>

    <script>
        const firebaseConfig = {
          apiKey: "AIzaSyC_hESDUgVpm-x_Ld5cE2gfMepz7SiwaGs",
          authDomain: "sky29-lounge.firebaseapp.com",
          projectId: "sky29-lounge",
          storageBucket: "sky29-lounge.firebasestorage.app",
          messagingSenderId: "265145556160",
          appId: "1:265145556160:web:441cb15f46a570695fa125",
          measurementId: "G-HWJBY59SE4"
        };
        
        firebase.initializeApp(firebaseConfig);
        const db = firebase.firestore();
    </script>

    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
        body {
            background-color: #050914;
            background-image: 
                radial-gradient(circle at 50% 20%, rgba(56, 189, 248, 0.25) 0%, transparent 60%),
                linear-gradient(rgba(5, 9, 20, 0.85), rgba(5, 9, 20, 0.92)),
                url('cityscape-of-george-town-2026-03-17-00-01-12-utc.jpg');
            background-position: center; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;
            color: #FFFFFF; padding-bottom: 90px; min-height: 100vh;
        }
        .app-container { display: flex; flex-direction: column; flex: 1; width: 100%; max-width: 500px; margin: 0 auto; padding: 20px; }
        .hero-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(56, 189, 248, 0.1));
            backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border-radius: 28px; border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 24px; margin-bottom: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }
        .badge-tag {
            background: rgba(56, 189, 248, 0.2); color: #38BDF8; font-size: 11px;
            padding: 5px 12px; border-radius: 20px; font-weight: 600; display: inline-block; margin-bottom: 10px;
        }
        .grid-action { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px; }
        .action-card {
            background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px);
            border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 18px; text-align: left; cursor: pointer; transition: all 0.2s ease;
        }
        .action-card:hover { background: rgba(56, 189, 248, 0.2); transform: translateY(-2px); }
        .facility-card {
            background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(20px);
            border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 16px; margin-bottom: 16px; display: flex; gap: 14px; align-items: center;
        }
        .facility-img { width: 85px; height: 85px; border-radius: 16px; object-fit: cover; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 12px; margin-bottom: 6px; color: rgba(255,255,255,0.8); }
        .form-group input {
            width: 100%; padding: 12px 16px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1); color: #fff; font-size: 13px; outline: none;
        }
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(5, 9, 20, 0.85); backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            display: flex; justify-style: space-around; padding: 12px 0; z-index: 1000;
        }
        .nav-item { text-align: center; color: rgba(255, 255, 255, 0.6); font-size: 11px; cursor: pointer; flex: 1; }
        .nav-item.active { color: #38BDF8; font-weight: 600; }
        .nav-item div { font-size: 18px; margin-bottom: 2px; }
        .section-tab { display: none; }
        .section-tab.active { display: block; }
        .btn-primary {
            width: 100%; padding: 14px; background: linear-gradient(135deg, #38BDF8, #0284C7);
            border: none; border-radius: 16px; color: #fff; font-weight: 600; cursor: pointer; margin-top: 12px;
        }
        .btn-danger {
            padding: 6px 12px; background: #EF4444; border: none; border-radius: 10px; color: #fff; font-size: 11px; font-weight: 600; cursor: pointer; margin-top: 8px;
        }
        .back-link {
            color: #38BDF8; font-size: 12px; text-decoration: none; display: inline-block; margin-bottom: 12px; cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- TAB UTAMA -->
        <div id="tab-utama" class="section-tab active">
            <div class="hero-card">
                <span class="badge-tag">SISTEM PINTAR</span>
                <h2>Selamat Datang,</h2>
                <p style="font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 16px;">Sistem Tempahan Lounge SKY29</p>
                <a href="login.php" style="color:#38BDF8; font-size:12px; text-decoration:none;">Portal Admin -></a>
            </div>
            <div class="grid-action">
                <div class="action-card" onclick="switchTab('tab-tempah')">
                    <div style="font-size:24px; margin-bottom:8px;">📅</div>
                    <strong>Tempah Fasiliti</strong>
                    <p style="font-size:11px; color:rgba(255,255,255,0.6);">Mula tempahan baru</p>
                </div>
                <div class="action-card" onclick="switchTab('tab-status')">
                    <div style="font-size:24px; margin-bottom:8px;">🔍</div>
                    <strong>Semak Status</strong>
                    <p style="font-size:11px; color:rgba(255,255,255,0.6);">Rekod tempahan</p>
                </div>
            </div>
            <h4 style="margin-bottom:12px;">Fasiliti Tersedia</h4>
            <div class="facility-card">
                <img src="cityscape-of-george-town-2026-03-17-00-01-12-utc.jpg" class="facility-img" alt="Lounge">
                <div>
                    <span class="badge-tag" style="margin-bottom:4px;">LOUNGE</span>
                    <h4 style="font-size:15px;">Lounge SKY29 Utama</h4>
                    <p style="font-size:11px; color:rgba(255,255,255,0.6);">Kapasiti: 30-50 Orang | Smart TV | Audio</p>
                    <button class="btn-primary" style="padding:6px 12px; font-size:11px; margin-top:8px;" onclick="switchTab('tab-tempah')">Pilih Fasiliti</button>
                </div>
            </div>
        </div>

        <!-- TAB TEMPAH -->
        <div id="tab-tempah" class="section-tab">
            <a onclick="switchTab('tab-utama')" class="back-link">← Kembali ke Utama</a>
            <h3 style="margin-bottom: 16px;">Tempah Lounge SKY29</h3>
            <form id="formTempahan">
                <div class="form-group">
                    <label>1. Pilih Tarikh Tempahan *</label>
                    <input type="date" id="tarikh" required>
                </div>
                <div class="form-group">
                    <label>2. Pilih Masa Mula & Tamat *</label>
                    <div style="display:flex; gap:10px;">
                        <input type="time" id="masa_mula" value="09:00" required>
                        <input type="time" id="masa_tamat" value="10:00" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>3. Nama Pemohon *</label>
                    <input type="text" id="nama" placeholder="Taip nama penuh" required>
                </div>
                <div class="form-group">
                    <label>4. Bahagian / Unit *</label>
                    <input type="text" id="bahagian" placeholder="Cth: BKP / Unit IT" required>
                </div>
                <div class="form-group">
                    <label>5. No. Telefon *</label>
                    <input type="text" id="no_tel" placeholder="012-XXXXXXX" required>
                </div>
                <div class="form-group">
                    <label>6. E-mel *</label>
                    <input type="email" id="email" placeholder="contoh@penang.gov.my" required>
                </div>
                <div class="form-group">
                    <label>7. Tujuan Tempahan *</label>
                    <input type="text" id="tujuan" placeholder="Cth: Mesyuarat Khas" required>
                </div>
                <button type="submit" class="btn-primary" id="btnHantar">Hantar Tempahan</button>
            </form>
        </div>

        <!-- TAB STATUS -->
        <div id="tab-status" class="section-tab">
            <a onclick="switchTab('tab-utama')" class="back-link">← Kembali ke Utama</a>
            <h3 style="margin-bottom: 16px;">Semak Status Tempahan</h3>
            <p style="font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 16px;">Masukkan No. Telefon atau E-mel yang digunakan semasa tempahan.</p>
            <div class="form-group">
                <input type="text" id="carian_status" placeholder="Masukkan No. Tel atau E-mel">
                <button class="btn-primary" onclick="semakStatus()">Semak Sekarang</button>
            </div>
            <div id="keputusan-status" style="margin-top:20px;"></div>
        </div>
    </div>

    <!-- BOTTOM NAV -->
    <div class="bottom-nav">
        <div class="nav-item active" onclick="switchTab('tab-utama')">
            <div>🏠</div>Utama
        </div>
        <div class="nav-item" onclick="switchTab('tab-tempah')">
            <div>📅</div>Tempah
        </div>
        <div class="nav-item" onclick="switchTab('tab-status')">
            <div>📑</div>Status
        </div>
    </div>

    <script>
        function switchTab(tabId, updateHistory = true) {
            var tabs = document.querySelectorAll('.section-tab');
            for(var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            var targetTab = document.getElementById(tabId);
            if(targetTab) {
                targetTab.classList.add('active');
            }

            var navs = document.querySelectorAll('.nav-item');
            for(var j = 0; j < navs.length; j++) {
                navs[j].classList.remove('active');
            }
            
            var activeNav = document.querySelector('.nav-item[onclick*="' + tabId + '"]');
            if(activeNav) {
                activeNav.classList.add('active');
            }

            if (updateHistory) {
                history.pushState({ tab: tabId }, '', '#' + tabId.replace('tab-', ''));
            }

            window.scrollTo(0, 0);
        }

        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.tab) {
                switchTab(e.state.tab, false);
            } else {
                switchTab('tab-utama', false);
            }
        });

        window.addEventListener('load', function() {
            var hash = window.location.hash.replace('#', '');
            if (hash && document.getElementById('tab-' + hash)) {
                switchTab('tab-' + hash, false);
            }
        });

        document.getElementById('formTempahan').addEventListener('submit', function(e) {
            e.preventDefault();
            
            var btn = document.getElementById('btnHantar');
            btn.innerText = "Sila tunggu...";
            btn.disabled = true;

            var nama = document.getElementById('nama').value;
            var bahagian = document.getElementById('bahagian').value;
            var no_tel = document.getElementById('no_tel').value;
            var email = document.getElementById('email').value;
            var tarikh = document.getElementById('tarikh').value;
            var masa_mula = document.getElementById('masa_mula').value;
            var masa_tamat = document.getElementById('masa_tamat').value;
            var tujuan = document.getElementById('tujuan').value;

            db.collection("tempahan")
              .where("tarikh", "==", tarikh)
              .get()
              .then(function(querySnapshot) {
                  var bertindih = false;
                  querySnapshot.forEach(function(doc) {
                      var d = doc.data();
                      if (d.status !== "DIBATALKAN") {
                          if (masa_mula < d.masa_tamat && masa_tamat > d.masa_mula) {
                              bertindih = true;
                          }
                      }
                  });

                  if (bertindih) {
                      alert("SLOT BERTINDIH! Masa ini telah ditempah oleh pengguna lain.");
                      btn.innerText = "Hantar Tempahan";
                      btn.disabled = false;
                      return;
                  }

                  db.collection("tempahan").add({
                      nama: nama,
                      bahagian: bahagian,
                      no_tel: no_tel,
                      email: email,
                      tarikh: tarikh,
                      masa_mula: masa_mula,
                      masa_tamat: masa_tamat,
                      tujuan: tujuan,
                      status: "PENDING",
                      created_at: firebase.firestore.FieldValue.serverTimestamp()
                  }).then(function(docRef) {
                      var booking_id = "#SKY" + docRef.id.substring(0, 5).toUpperCase();

                      db.collection("audit_log").add({
                          tempahan_id: booking_id,
                          tindakan: "PERMOHONAN_BARU",
                          dilakukan_oleh: nama + " (Pemohon)",
                          tarikh_masa: new Date().toLocaleString('ms-MY')
                      });

                      alert("Tempahan Berjaya Dihantar! ID Tempahan Anda: " + booking_id);
                      document.getElementById('formTempahan').reset();
                      btn.innerText = "Hantar Tempahan";
                      btn.disabled = false;
                      switchTab('tab-status');
                  });
              });
        });

        function semakStatus() {
            var input = document.getElementById('carian_status').value.trim();
            if(!input) { alert('Sila masukkan No. Tel atau E-mel'); return; }
            
            var container = document.getElementById('keputusan-status');
            container.innerHTML = '<p style="font-size:12px; color:rgba(255,255,255,0.6);">Mencari rekod...</p>';

            db.collection("tempahan").get().then(function(querySnapshot) {
                container.innerHTML = '';
                var jumpa = false;

                querySnapshot.forEach(function(doc) {
                    var item = doc.data();
                    if (item.no_tel === input || item.email === input) {
                        jumpa = true;
                        var docId = doc.id;
                        var booking_id = "#SKY" + docId.substring(0, 5).toUpperCase();

                        var badgeColor = '#FBBF24';
                        var badgeBg = 'rgba(245, 158, 11, 0.2)';
                        if(item.status === 'LULUS') { badgeColor = '#34D399'; badgeBg = 'rgba(52, 211, 153, 0.2)'; }
                        if(item.status === 'DIBATALKAN') { badgeColor = '#F87171'; badgeBg = 'rgba(248, 113, 113, 0.2)'; }

                        var cancelBtn = '';
                        if (item.status !== 'DIBATALKAN') {
                            cancelBtn = '<button class="btn-danger" onclick="batalkanSendiri(\'' + docId + '\', \'' + item.nama + '\', \'' + booking_id + '\')">Batalkan Tempahan Ini</button>';
                        }

                        var itemBox = document.createElement('div');
                        itemBox.className = 'facility-card';
                        itemBox.style.flexDirection = 'column';
                        itemBox.style.alignItems = 'flex-start';
                        itemBox.innerHTML = 
                            '<div style="display:flex; justify-content:space-between; width:100%;">' +
                                '<strong>' + booking_id + '</strong>' +
                                '<span class="badge-tag" style="background:' + badgeBg + '; color:' + badgeColor + ';">' + item.status + '</span>' +
                            '</div>' +
                            '<p style="font-size:12px; margin-top:6px;">Tarikh: ' + item.tarikh + ' (' + item.masa_mula + ' - ' + item.masa_tamat + ')</p>' +
                            '<p style="font-size:11px; color:rgba(255,255,255,0.6);">Tujuan: ' + item.tujuan + '</p>' +
                            cancelBtn;
                        container.appendChild(itemBox);
                    }
                });

                if(!jumpa) {
                    container.innerHTML = '<p style="font-size:12px; color:rgba(255,255,255,0.6);">Tiada rekod dijumpai.</p>';
                }
            });
        }

        function batalkanSendiri(docId, namaPemohon, bookingId) {
            if (confirm('Adakah anda pasti untuk membatalkan tempahan ini?')) {
                db.collection("tempahan").doc(docId).update({
                    status: "DIBATALKAN"
                }).then(function() {
                    db.collection("audit_log").add({
                        tempahan_id: bookingId,
                        tindakan: "PEMBATALAN_PEMOHON",
                        dilakukan_oleh: namaPemohon + " (Pemohon)",
                        tarikh_masa: new Date().toLocaleString('ms-MY')
                    });

                    alert('Tempahan anda telah dibatalkan.');
                    semakStatus();
                });
            }
        }
    </script>
</body>
</html>
