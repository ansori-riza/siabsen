# ZKTeco Integration Research

**Tanggal:** 23 Maret 2026
**Status:** Research Phase - Protocol Analysis

---

## I. ZKTECO PROTOCOL OVERVIEW

### A. Jenis Protocol

ZKTeco mendukung 2 protocol utama:

#### 1. **Pull SDK** (Client-Pull)
- **Port Default:** 4370 (TCP)
- **Metode:** Server aktif connect ke device untuk ambil data
- **Use Case:** Scheduled sync (tiap 5 menit, tiap jam)
- **Kelebihan:** Simple, bisa di-schedule
- **Kekurangan:** Tidak real-time, harus polling berkala

#### 2. **Push SDK** (Device-Push / ADMS)
- **Port Default:** 80/8080 (HTTP)
- **Metode:** Device aktif kirim data ke server saat event terjadi
- **Use Case:** Real-time attendance monitoring
- **Kelebihan:** Real-time, event-driven
- **Kekurangan:** Lebih kompleks, butuh server yang reachable dari device

---

## II. TECHNICAL SPECIFICATIONS

### A. Pull SDK Details

**Connection String Format:**
```
protocol=TCP,ipaddress=192.168.1.201,port=4370,timeout=4000,passwd=
```

**Supported Operations:**
- Connect/disconnect device
- Get attendance logs
- Get user data (PIN, name, fingerprint)
- Set user data
- Sync time
- Get device info (SN, firmware)
- Clear attendance logs

**Python Libraries:**
1. **pyzk** - Most popular, unofficial Python SDK
2. **pyzatt** - Alternative library for TFT/iFace models
3. **pyzkaccess** - For ZKAccess access control devices
4. **zkconnect** - Microservice for real-time sync

**Laravel Package:**
- `laradevsbd/zkteco-sdk-laravel` - Laravel wrapper for Pull SDK

### B. Push SDK Details

**Communication Protocol:** HTTP-based
**Device → Server Messages:**
- Upload attendance records
- Upload attendance photos
- Upload system logs
- Upload user info (basic info, fingerprint, face)
- Delete oldest data (overflow management)

**Server → Device Messages:**
- Check data update conditions
- Purge attendance records/photos
- Download firmware
- Download SMS messages
- Add/modify/delete users
- Download user photos
- Correct attendance data

**Configuration:**
- Set server IP/Port di device
- Device akan auto-connect ke server

---

## III. AVAILABLE LIBRARIES & TOOLS

### A. Python Libraries (Recommended)

#### 1. pyzk (Most Popular)
```python
from zk import ZK, const

conn = None
zk = ZK('192.168.1.201', port=4370, timeout=5)
try:
    conn = zk.connect()
    
    # Get attendance
    attendances = conn.get_attendance()
    for attendance in attendances:
        print(f"User: {attendance.user_id}, Time: {attendance.timestamp}")
    
    # Get users
    users = conn.get_users()
    for user in users:
        print(f"UID: {user.uid}, Name: {user.name}")
        
    # Disconnect
    conn.disconnect()
except Exception as e:
    print(f"Error: {e}")
finally:
    if conn:
        conn.disconnect()
```

**Install:**
```bash
pip install pyzk
```

**GitHub:** https://github.com/fananimi/pyzk

#### 2. pyzatt (For TFT/iFace Models)
```python
from pyzatt.lib import Zklib

zk = Zklib()
zk.connect("192.168.1.201", 4370)
attendance = zk.getAttendance()
zk.disconnect()
```

**GitHub:** https://github.com/adrobinoga/pyzatt

#### 3. zkconnect (Microservice)
- Real-time sync dari device ke API
- Running as service (Supervisor)
- Tested dengan F18 dan K40 models

**GitHub:** https://github.com/sowrensen/zkconnect

### B. Laravel Package

#### laradevsbd/zkteco-sdk-laravel
```php
use Laradevsbd\Zkteco\Http\Library\ZktecoLib;

$zk = new ZktecoLib(config('zkteco.ip'), config('zkteco.port'));

if ($zk->connect()) {
    $attendance = $zk->getAttendance();
    return view('zkteco::app', compact('attendance'));
}
```

**Install:**
```bash
composer require laradevsbd/zkteco-sdk-laravel
```

**GitHub:** https://github.com/laradevsbd/zkteco-sdk-laravel

### C. Third-Party Solutions

#### ZktecoApi.com
- Cloud-based API service
- RESTful API
- Real-time sync
- 24/7 support
- Pricing: Subscription-based

**Website:** https://zktecoapi.com/

#### AuraSofts ZKTeco Push SDK
- Complete Push SDK implementation
- Web API (Windows/Linux)
- Compatible with any solution
- Includes API documentation, Postman collection, DB structure

**Website:** http://aurasofts.com/zkteco-fingerprint-attendance/

---

## IV. PROTOCOL TECHNICAL DETAILS

### A. Pull SDK Protocol

**Packet Structure:**
- Header: 2 bytes (0x50, 0x50)
- Size: 2 bytes
- Command: 2 bytes
- Data: Variable
- Checksum: 2 bytes

**Commands:**
- `CMD_CONNECT` (0x03e8)
- `CMD_EXIT` (0x03e9)
- `CMD_ENABLEDEVICE` (0x03ea)
- `CMD_GET_TIME` (0x03e2)
- `CMD_SET_TIME` (0x0302)
- `CMD_GET_ATTENDANCE` (0x03b4)
- `CMD_GET_USER` (0x05c3)
- `CMD_SET_USER` (0x03e3)
- `CMD_CLEAR_DATA` (0x0342)

**Detailed Protocol:** https://github.com/adrobinoga/zk-protocol

### B. Push SDK Protocol

**HTTP Endpoints:**

1. **Device → Server (POST)**
   - `/iclock/cdata` - Upload attendance
   - `/iclock/userdata` - Upload user info
   - `/iclock/fdata` - Upload fingerprint
   - `/iclock/photo` - Upload photo

2. **Server → Device (Response)**
   - XML/JSON response
   - Commands for device

**Protocol Doc:** 
- PUSH SDK Communication Protocol V2.0.1.pdf
- Available on Scribd and CourseHero

---

## V. HARDWARE COMPATIBILITY

### A. Pull SDK Compatible Devices
- F18
- K40
- K40 Pro
- iFace402
- SpeedFace series
- Most standalone devices

### B. Push SDK Compatible Devices
- SpeedFace series
- ProFace series
- ELITE PASS terminals
- Inbio Pro controllers
- C2-260 controllers

### C. Requirements
- Device dengan network capability
- Static IP (recommended)
- Firmware yang support SDK version
- Network reachable dari server

---

## VI. IMPLEMENTATION APPROACH

### A. Phase 1: Pull SDK (Recommended Start)

**Alasan:**
- Lebih simple dan straightforward
- Banyak contoh kode tersedia
- Tidak butuh konfigurasi device yang kompleks
- Cocok untuk development awal

**Komponen:**
1. Python service (pyzk/pyzatt)
2. Scheduled job (tiap 5 menit)
3. Mapping ke API SiAbsen

**Flow:**
```
Scheduled Job → pyzk → ZKTeco Device → Attendance Data
↓
Mapping to SiAbsen Format
↓
POST to /api/v1/absensi
```

### B. Phase 2: Push SDK (Advanced)

**Alasan:**
- Real-time data
- Event-driven
- Lebih reliable untuk production

**Komponen:**
1. HTTP server endpoint
2. Protocol handler
3. Device configuration service
4. Real-time event processing

**Flow:**
```
ZKTeco Device (event) → HTTP POST → SiAbsen Server
↓
Parse Push Protocol
↓
Save to database + trigger notifications
```

---

## VII. RECOMMENDATION

### A. Next Steps

1. **Install pyzk library** untuk testing
2. **Test dengan device** (jika ada unit)
3. **Create adapter service** di SiAbsen
4. **Implement mapping layer** untuk konversi format
5. **Add to Perangkat model** dengan vendor_type='zkteco'

### B. Development Tasks

**Pull SDK Adapter:**
- [ ] Create `ZKTecoPullAdapter` class
- [ ] Implement connection management
- [ ] Implement attendance sync
- [ ] Map ZKTeco user_id to SiAbsen user
- [ ] Add error handling & retry logic
- [ ] Create scheduled job

**Push SDK Adapter:**
- [ ] Create HTTP endpoint `/api/zkteco/push`
- [ ] Parse Push protocol messages
- [ ] Handle device registration
- [ ] Process real-time events
- [ ] Implement security validation

### C. Estimated Effort

**Pull SDK:**
- Setup & Testing: 2-3 hari
- Adapter Development: 3-4 hari
- Integration: 2-3 hari
- Testing & Debug: 1-2 hari
- **Total: 8-12 hari**

**Push SDK:**
- Protocol Analysis: 2-3 hari
- Endpoint Development: 4-5 hari
- Integration: 3-4 hari
- Testing & Debug: 2-3 hari
- **Total: 11-15 hari**

---

## VIII. REFERENCES

### A. GitHub Repositories
1. https://github.com/fananimi/pyzk - Python Pull SDK
2. https://github.com/adrobinoga/pyzatt - Alternative library
3. https://github.com/adrobinoga/zk-protocol - Protocol documentation
4. https://github.com/sowrensen/zkconnect - Microservice example
5. https://github.com/laradevsbd/zkteco-sdk-laravel - Laravel package

### B. Documentation
1. PullSDK User Guide V2.0
2. PUSH SDK Communication Protocol V2.0.1
3. ZK Protocol Specification

### C. Third-Party Services
1. https://zktecoapi.com/ - Cloud API service
2. http://aurasofts.com/zkteco-fingerprint-attendance/ - Push SDK solution

---

*Dokumen ini akan diupdate setelah implementasi dimulai.*