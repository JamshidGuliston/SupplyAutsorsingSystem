import { launchCamera, ImagePickerResponse } from 'react-native-image-picker';

export interface CapturedSelfie {
  uri: string;
  fileName: string;
  type: string;
}

export async function captureSelfie(): Promise<CapturedSelfie> {
  return new Promise((resolve, reject) => {
    launchCamera(
      {
        mediaType: 'photo',
        cameraType: 'front',
        quality: 0.7,
        saveToPhotos: false,
        includeBase64: false,
      },
      (response: ImagePickerResponse) => {
        if (response.didCancel) {
          reject(new Error('Bekor qilindi'));
          return;
        }
        if (response.errorCode) {
          reject(new Error(`Kamera xatosi: ${response.errorMessage || response.errorCode}`));
          return;
        }
        const asset = response.assets?.[0];
        if (!asset?.uri) {
          reject(new Error('Rasm olinmadi'));
          return;
        }
        resolve({
          uri: asset.uri,
          fileName: asset.fileName ?? `selfie_${Date.now()}.jpg`,
          type: asset.type ?? 'image/jpeg',
        });
      },
    );
  });
}
