package com.cpdms.util;

import java.io.InputStream;
import java.security.cert.CertificateException;
import java.security.spec.KeySpec;
import java.security.spec.PKCS8EncodedKeySpec;
import java.security.spec.X509EncodedKeySpec;
import java.util.*;
import java.security.*;
import java.security.cert.Certificate;

import javax.crypto.Cipher;
import javax.crypto.spec.SecretKeySpec;
import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLSession;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;
import javax.ws.rs.core.MediaType;

import org.apache.commons.codec.binary.Base64;

import com.google.gson.Gson;
import com.sun.jersey.api.client.Client;
import com.sun.jersey.api.client.ClientResponse;
import com.sun.jersey.api.client.WebResource;
import com.sun.jersey.api.client.WebResource.Builder;
import com.sun.jersey.api.client.config.DefaultClientConfig;
import com.sun.jersey.client.urlconnection.HTTPSProperties;
import org.apache.commons.io.IOUtils;
import org.springframework.core.io.ClassPathResource;

public class UnionpayUtils {
	private static Client c = null;
	private static Client secureClient = null;
	private static Gson gson = new Gson();
	private static String userPrivateKey;
	private static String userPublicKey;
	private static String njPrivateKey;

	/**
	 * 初始化设置
	 */
	static {
		initalizationJersyClient();
		initalizationSecureJersyClient();
		initalizationKeys();
	}

	/**
	 * 设置调用参数
	 */
	private static void initalizationJersyClient() {
		try {
			c = Client.create();
			c.setFollowRedirects(true);
			c.setConnectTimeout(10000);
			c.setReadTimeout(10000);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	private static void initalizationSecureJersyClient() {
		try {
			SSLContext context = SSLContext.getInstance("SSL");
			context.init(null, new TrustManager[] { new X509TrustManager() {
				public java.security.cert.X509Certificate[] getAcceptedIssuers() {
					return null;
				}

				@Override
				public void checkClientTrusted(java.security.cert.X509Certificate[] chain, String authType)
						throws CertificateException {
				}

				@Override
				public void checkServerTrusted(java.security.cert.X509Certificate[] chain, String authType)
						throws CertificateException {
				}
			} }, new SecureRandom());
			HostnameVerifier hv = new HostnameVerifier() {
				public boolean verify(String urlHostName, SSLSession session) {
					return true;
				}
			};

			HTTPSProperties prop = new HTTPSProperties(hv, context);
			DefaultClientConfig dcc = new DefaultClientConfig();
			dcc.getProperties().put(HTTPSProperties.PROPERTY_HTTPS_PROPERTIES, prop);
			secureClient = Client.create(dcc);
			secureClient.setFollowRedirects(true);
			secureClient.setConnectTimeout(10000);
			secureClient.setReadTimeout(10000);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	private static void initalizationKeys()  {
		try {
			String password = "111111";
			ClassPathResource privateResource = new ClassPathResource("user-rsa.pfx");
			// 实例化密钥库，默认JKS类型
			KeyStore ks = KeyStore.getInstance("PKCS12");
			// 获得密钥库文件流
			InputStream is = privateResource.getInputStream();
			// 加载密钥库
			ks.load(is, password.toCharArray());
			// 关闭密钥库文件流
			is.close();
			//私钥
			Enumeration aliases = ks.aliases();
			String keyAlias = null;
			if (aliases.hasMoreElements()){
				keyAlias = (String)aliases.nextElement();
			}
			PrivateKey privateKey = (PrivateKey) ks.getKey(keyAlias, password.toCharArray());
			String privateKeyStr = Base64.encodeBase64String(privateKey.getEncoded());
			userPrivateKey = privateKeyStr;

			//公钥
			Certificate certificate = ks.getCertificate(keyAlias);
			String publicKeyStr = Base64.encodeBase64String(certificate.getPublicKey().getEncoded());
			userPublicKey = publicKeyStr;

			// 行員私鑰
			ClassPathResource njPrivateResource = new ClassPathResource("private.key");
			String njPrivateKeyStr = IOUtils.toString(njPrivateResource.getInputStream(), "utf-8");
			njPrivateKey = njPrivateKeyStr.replaceAll("\r\n", "");
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/**
	 * 调用接口
	 */
	public static String sendPOSTRequest(String url, Object map, String contentTpye) {
		if (null == c) {
			initalizationJersyClient();
		}
		Client client = null;
		if (url.indexOf("https://") == 0) {
			if (null == secureClient) {
				initalizationJersyClient();
			}
			client = secureClient;
		} else {
			if (null == c) {
				initalizationJersyClient();
			}
			client = c;
		}
		WebResource resource = client.resource(url);
		String resultStr = null;
		try {
			Builder builder = resource.accept("*/*");
			ClientResponse res = builder.type(contentTpye).entity(map).post(ClientResponse.class);
			if (res.getStatus() != 200) {
				throw new Exception("url:" + url + ",response code:" + res.getStatus());
			}
			resultStr = res.getEntity(String.class);
			return resultStr;
		} catch (Exception e) {
			e.printStackTrace();
			return null;
		}
	}

	/**
	 * 参数 转为json格式
	 *
	 * @param url 调用地址
	 * @param map 设置的参数
	 * @return
	 */
	public static String sendPostGson(String url, Map<String, String> map) {
		String ps = gson.toJson(map);
		return sendPOSTRequest(url, ps, MediaType.APPLICATION_JSON);
	}

	/**
	 * 使用公钥加密对称密钥
	 *
	 * @param publicKey        公钥
	 * @param symmetricKeyByte 对称密钥字节
	 * @return 加密后的对称密钥字节
	 * @throws Exception
	 */
	public static byte[] encrypt(String publicKey, byte[] symmetricKeyByte) throws Exception {
		byte[] encodedKey = Base64.decodeBase64(publicKey);
		KeySpec keySpec = new X509EncodedKeySpec(encodedKey);
		KeyFactory keyFactory = KeyFactory.getInstance("RSA");
		PublicKey pk = keyFactory.generatePublic(keySpec);
		Cipher cipher = Cipher.getInstance("RSA/ECB/PKCS1Padding");
		cipher.init(Cipher.ENCRYPT_MODE, pk);
		byte[] result = cipher.doFinal(symmetricKeyByte);
		return result;
	}

	/**
	 * 签名加密后的数据装载进map
	 *
	 * @param key         对称秘钥
	 * @param params      待加密的字符串
	 * @param encryptKeys 加密字段
	 * @throws Exception
	 */
	public static void encryptedParamMap(String key, Map<String, String> params, String... encryptKeys)
			throws Exception {
		if (encryptKeys != null && encryptKeys.length > 0) {
			for (String encryptKey : encryptKeys) {
				params.put(encryptKey, getEncryptedValue(params.get(encryptKey), key));
			}
		}
	}

	/**
	 * 3DES加密
	 *
	 * @param value 待加密的字符串
	 * @param key   加密密钥
	 * @return 加密后的字符串
	 * @throws Exception
	 */
	private static String getEncryptedValue(String value, String key) throws Exception {
		if (null == value || "".equals(value)) {
			return "";
		}
		byte[] valueByte = value.getBytes();
		byte[] sl = encrypt3DES(valueByte, hexToBytes(key));
		String result = Base64.encodeBase64String(sl);
		return result;
	}

	/**
	 * 3DES加密
	 *
	 * @param input 待加密的字节
	 * @param key   密钥
	 * @return 加密后的字节
	 * @throws Exception
	 */
	private static byte[] encrypt3DES(byte[] input, byte[] key) throws Exception {
		Cipher c = Cipher.getInstance("DESede/ECB/PKCS5Padding");
		c.init(Cipher.ENCRYPT_MODE, new SecretKeySpec(key, "DESede"));
		return c.doFinal(input);
	}

	/**
	 * 获取随机字符串
	 *
	 * @return 随机字符串
	 */
	public static String createNonceStr() {
		String sl = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		StringBuilder sb = new StringBuilder();
		for (int i = 0; i < 16; i++) {
			sb.append(sl.charAt(new Random().nextInt(sl.length())));
		}
		return sb.toString();
	}

	/**
	 * 签名
	 *
	 * @param param   待签名的参数
	 * @param signKey 签名密钥
	 * @return 签名结果字符串
	 * @throws Exception
	 */
	public static String sign(Map<String, String> param, String signKey) throws Exception {
		String value = sortMap(param);
		byte[] keyBytes = Base64.decodeBase64(signKey);
		PKCS8EncodedKeySpec keySpec = new PKCS8EncodedKeySpec(keyBytes);
		KeyFactory keyf = KeyFactory.getInstance("RSA");
		PrivateKey priKey = keyf.generatePrivate(keySpec);
		Signature signature = Signature.getInstance("SHA256WithRSA");
		signature.initSign(priKey);
		signature.update(value.getBytes());
		byte[] signed = signature.sign();
		String result = Base64.encodeBase64String(signed);
		return result;
	}

	/**
	 * 签名
	 *
	 * @param param   待签名的参数
	 * @param signKey 签名密钥
	 * @return 签名结果字符串
	 * @throws Exception
	 */
	public static String signForUnionpay(Map<String, String> param, String signKey) throws Exception {
	    // 1.对交易报文中所有非空请求参数(sign不参与签名)拼接成key=value&key=value格式并按字符顺序进行升序排序
		String value = sortMap(param);
		// 2.将转换好的字符串进行SHA256摘要处理,对摘要数据进行16进制处理。最后，把16进制数据全部转成小写。
		String paramStr = sha256(value.getBytes());
        // 3.使用签名私钥对上面处理后的数据进行签名,签名后再将数据进行base64编码。
		byte[] keyBytes = Base64.decodeBase64(signKey);
		PKCS8EncodedKeySpec keySpec = new PKCS8EncodedKeySpec(keyBytes);
		KeyFactory keyf = KeyFactory.getInstance("RSA");
		PrivateKey priKey = keyf.generatePrivate(keySpec);
		Signature signature = Signature.getInstance("SHA256WithRSA");
		signature.initSign(priKey);
		signature.update(paramStr.getBytes());
		byte[] signed = signature.sign();

		return Base64.encodeBase64String(signed);
	}

	/**
	 * 排序
	 *
	 * @param param 待排序的参数
	 * @return 排序结果字符串
	 */
	public static String sortMap(Map<String, String> param) {
		StringBuilder result = new StringBuilder();
		Collection<String> keySet = param.keySet();
		List<String> list = new ArrayList<String>(keySet);
		Collections.sort(list);
		for (int i = 0; i < list.size(); ++i) {
			String key = list.get(i);
			if ("symmetricKey".equals(key)) {
				continue;
			}
			if (param.get(key) == null || "".equals(param.get(key).trim())) {
				continue;
			}
			result.append(key).append("=").append(param.get(key)).append("&");
		}
		return result.substring(0, result.length() - 1);
	}

	public static String sha256(byte[] data) {
		try {
			MessageDigest md = MessageDigest.getInstance("SHA-256");
			return bytesToHex(md.digest(data));

		} catch (Exception ex) {

			return null;
		}
	}

	/**
	 * 将byte数组转换成16进制字符串
	 *
	 * @param bytes
	 * @return 16进制字符串
	 */
	public static String bytesToHex(byte[] bytes) {

		/*
		 * StringBuilder sb = new StringBuilder(bytes.length * 2); String hexArray =
		 * "0123456789ABCDEF"; for (byte b : bytes) { int bi = b & 0xff;
		 * sb.append(hexArray.charAt(bi >> 4)); sb.append(hexArray.charAt(bi & 0xf)); }
		 * return sb.toString();
		 */

		  char[] hexCode = "0123456789abcdef".toCharArray();
		  StringBuilder sb = new
		  StringBuilder(bytes.length * 2);
		  for (byte b : bytes) {
			  sb.append(hexCode[(b >> 4) & 0xF]);
			  sb.append(hexCode[(b & 0xF)]);
		  }

		  return sb.toString();

	}

	public static byte[] hexToBytes(String hex) {
		return hexToBytes(hex.toCharArray());
	}

	public static byte[] hexToBytes(char[] hex) {
		int length = hex.length / 2;
		byte[] raw = new byte[length];
		for (int i = 0; i < length; i++) {
			int high = Character.digit(hex[i * 2], 16);
			int low = Character.digit(hex[i * 2 + 1], 16);
			int value = (high << 4) | low;
			if (value > 127) {
				value -= 256;
			}
			raw[i] = (byte) value;
		}
		return raw;
	}

	public static String getPrivateKey() {
		return userPrivateKey;
	}

	public static String getNjPrivateKey() {
		return njPrivateKey;
	}

	public static String getPublicKey() {
		return userPublicKey;
	}
}
