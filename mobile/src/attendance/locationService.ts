import Geolocation from 'react-native-geolocation-service';

export interface FixedLocation {
  lat: number;
  lng: number;
  accuracyM: number;
  timestamp: number;
  isMock: boolean;
}

export async function getCurrentLocation(timeoutMs: number = 10_000): Promise<FixedLocation> {
  return new Promise((resolve, reject) => {
    Geolocation.getCurrentPosition(
      (pos) => {
        resolve({
          lat: pos.coords.latitude,
          lng: pos.coords.longitude,
          accuracyM: pos.coords.accuracy,
          timestamp: pos.timestamp,
          isMock: (pos as any).mocked === true,
        });
      },
      (err) => reject(new Error(`GPS error (${err.code}): ${err.message}`)),
      {
        enableHighAccuracy: true,
        timeout: timeoutMs,
        maximumAge: 0,
        forceRequestLocation: true,
      },
    );
  });
}
