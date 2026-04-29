package uz.kindergarden.chefmobile.mockgps

import android.provider.Settings
import com.facebook.react.bridge.Promise
import com.facebook.react.bridge.ReactApplicationContext
import com.facebook.react.bridge.ReactContextBaseJavaModule
import com.facebook.react.bridge.ReactMethod

class MockGpsModule(reactContext: ReactApplicationContext) : ReactContextBaseJavaModule(reactContext) {
    override fun getName(): String = "MockGps"

    /**
     * Returns true if mock-location is currently enabled in developer settings.
     * On Android < 6 this reflects ALLOW_MOCK_LOCATION; on newer versions
     * this flag is less reliable and per-location `isFromMockProvider()`
     * checks (done JS-side via geolocation lib's `mocked` field) cover the rest.
     */
    @ReactMethod
    fun isAllowed(promise: Promise) {
        try {
            val mockSetting = Settings.Secure.getString(
                reactApplicationContext.contentResolver,
                Settings.Secure.ALLOW_MOCK_LOCATION
            )
            val allowed = mockSetting == "1"
            promise.resolve(allowed)
        } catch (e: Exception) {
            promise.resolve(false)
        }
    }
}
