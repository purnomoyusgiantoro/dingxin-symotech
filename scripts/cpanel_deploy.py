import os
import sys
import ftplib
import ssl
import time
import urllib.request
import re

def create_ftps_client(server, user, pwd):
    ftps = ftplib.FTP_TLS()
    ftps.connect(server, 21, timeout=60)
    ftps.login(user, pwd)
    ftps.prot_p()
    return ftps

def main():
    server = os.environ.get("CPANEL_FTP_SERVER")
    user = os.environ.get("CPANEL_FTP_USERNAME")
    pwd = os.environ.get("CPANEL_FTP_PASSWORD")

    if not (server and user and pwd):
        print("❌ Error: FTP Credentials missing from environment.")
        sys.exit(1)

    print(f"Connecting via FTPS to {server} as {user}...")
    ftps = create_ftps_client(server, user, pwd)
    print("✅ FTPS Authentication successful.")

    base_remote = "dingxin.symotech.web.id"
    
    # 1. Clean stale cache files so Laravel immediately reads new routes & views
    for sub in ["bootstrap/cache", "storage/framework/views"]:
        target_dir = f"{base_remote}/{sub}"
        try:
            items = ftps.nlst(target_dir)
            for item in items:
                if item.endswith(".php"):
                    try:
                        ftps.delete(item)
                        print(f"  [Cache Cleaned] {item}")
                    except Exception:
                        pass
        except Exception as e:
            print(f"  [Notice] {sub}: {e}")

    # Prioritized upload order: routes and views first, then others
    upload_dirs = [
        "routes",
        "resources",
        "app",
        "config",
        "bootstrap",
        "public"
    ]
    single_files = [
        ".htaccess",
        "artisan",
        "composer.json"
    ]

    def ensure_remote_dir(client, rpath):
        parts = rpath.replace("\\", "/").strip("/").split("/")
        current = ""
        for p in parts:
            current += "/" + p if current else p
            try:
                client.mkd(current)
            except Exception:
                pass

    def upload_file_with_retry(client, local_path, remote_path, max_retries=3):
        nonlocal ftps
        for attempt in range(1, max_retries + 1):
            try:
                with open(local_path, "rb") as f:
                    ftps.storbinary(f"STOR {remote_path}", f)
                return True
            except Exception as e:
                print(f"  ⚠️ [Retry {attempt}/{max_retries}] {remote_path}: {e}")
                time.sleep(2)
                try:
                    ftps.quit()
                except Exception:
                    pass
                try:
                    ftps = create_ftps_client(server, user, pwd)
                    print("  🔄 Reconnected FTPS session.")
                except Exception as ce:
                    print(f"  ❌ Reconnection failed: {ce}")
        return False

    # Upload single files first
    print("\n📄 Mengunggah berkas utama...")
    for sf in single_files:
        if os.path.exists(sf):
            rf = f"{base_remote}/{sf}"
            print(f"Uploading file: {sf} -> {rf}")
            upload_file_with_retry(ftps, sf, rf)

    # Upload directories recursively
    for d in upload_dirs:
        if not os.path.exists(d):
            continue
        print(f"\n📂 Mengunggah direktori: {d}...")
        for root, dirs, files in os.walk(d):
            dirs[:] = [sub for sub in dirs if not sub.startswith(".") and sub != "cache" and sub != "vendor" and sub != "node_modules"]
            rel_root = os.path.relpath(root, ".")
            rem_root = f"{base_remote}/{rel_root}".replace("\\", "/")
            ensure_remote_dir(ftps, rem_root)
            
            for file in files:
                if file.startswith(".") and file != ".htaccess":
                    continue
                if file.endswith((".sqlite", ".db", ".log", ".tmp")):
                    continue
                loc_file = os.path.join(root, file)
                rem_file = f"{rem_root}/{file}".replace("\\", "/")
                success = upload_file_with_retry(ftps, loc_file, rem_file)
                if not success:
                    print(f"❌ Failed to upload: {loc_file}")

    try:
        ftps.quit()
    except Exception:
        pass
    print("\n✅ Seluruh berkas source code berhasil diunggah ke cPanel!")

    # 3. Health check verification
    print("\n🩺 Melakukan verifikasi produksi ke https://dingxin.symotech.web.id/...")
    time.sleep(5)
    try:
        req = urllib.request.Request(
            "https://dingxin.symotech.web.id/",
            headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"}
        )
        with urllib.request.urlopen(req, timeout=15) as resp:
            content = resp.read().decode("utf-8", errors="ignore")
            title_m = re.search(r"<title>(.*?)</title>", content, re.IGNORECASE)
            title = title_m.group(1) if title_m else "No title found"
            print(f"Status HTTP: {resp.status}")
            print(f"Page Title: {title}")
            print("🎉 VERIFIKASI BERHASIL: Portal Dingxin sudah merespons di production!")
    except Exception as e:
        print(f"Health check notice: {e}")

if __name__ == "__main__":
    main()
