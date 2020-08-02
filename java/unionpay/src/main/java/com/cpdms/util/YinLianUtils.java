package com.cpdms.util;

import org.apache.commons.io.IOUtils;
import org.springframework.core.io.ClassPathResource;
import sun.misc.BASE64Decoder;
import sun.misc.BASE64Encoder;

import javax.crypto.Cipher;
import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.IOException;
import java.nio.charset.Charset;
import java.security.*;
import java.security.interfaces.RSAPrivateKey;
import java.security.interfaces.RSAPublicKey;
import java.security.spec.InvalidKeySpecException;
import java.security.spec.PKCS8EncodedKeySpec;
import java.security.spec.X509EncodedKeySpec;

public class YinLianUtils {

    public static final String RSA_ALGORITHM = "RSA";
    public static final Charset UTF8 = Charset.forName("UTF-8");

    // 使用指定私钥解密
    public static String decrypt(String content) {
        ClassPathResource classPathResource = new ClassPathResource("private.key");
        byte[] decrypted;
        try {
            String privateKeyStr = IOUtils.toString(classPathResource.getInputStream(), "utf-8");
            PrivateKey privateKey = loadPrivateKey(privateKeyStr.replaceAll("\r\n", ""));
            decrypted = decrypt(privateKey, content.getBytes());
            return new String(decrypted, UTF8);
        } catch (Exception e) {
            return "";
        }
    }

    // 使用指定公钥加密
    public static String encrypt(String content) {
        ClassPathResource classPathResource = new ClassPathResource("public.key");
        byte[] encrypted;
        try {
            String publicKeyStr = IOUtils.toString(classPathResource.getInputStream(), "utf-8");
            PublicKey publicKey = YinLianUtils.loadPublicKey(publicKeyStr.replaceAll("\r\n", ""));
            encrypted = encrypt(publicKey, content);
            return base64Encode(encrypted);
        } catch (Exception e) {
            return "";
        }


    }

    // 生成一对密钥
    public static KeyPair buildKeyPair() throws NoSuchAlgorithmException {
        final int keySize = 2048;
        KeyPairGenerator keyPairGenerator = KeyPairGenerator.getInstance(RSA_ALGORITHM);
        keyPairGenerator.initialize(keySize);
        return keyPairGenerator.genKeyPair();
    }

    public static byte[] encrypt(PublicKey publicKey, String message) throws Exception {
        Cipher cipher = Cipher.getInstance(RSA_ALGORITHM);
        cipher.init(Cipher.ENCRYPT_MODE, publicKey);

        return cipher.doFinal(message.getBytes(UTF8));
    }

    public static byte[] decrypt(PrivateKey privateKey, byte [] encrypted) throws Exception {
        Cipher cipher = Cipher.getInstance(RSA_ALGORITHM);
        cipher.init(Cipher.DECRYPT_MODE, privateKey);

        return cipher.doFinal(encrypted);
    }

    /**
     * 从字符串中加载公钥
     *
     */
    public static RSAPublicKey loadPublicKey(String publicKeyStr) throws Exception {
        try {
            byte[] buffer = base64Decode(publicKeyStr);
            KeyFactory keyFactory = KeyFactory.getInstance(RSA_ALGORITHM);
            X509EncodedKeySpec keySpec = new X509EncodedKeySpec(buffer);
            return (RSAPublicKey) keyFactory.generatePublic(keySpec);
        } catch (NoSuchAlgorithmException e) {
            throw new RuntimeException(e);
        } catch (InvalidKeySpecException e) {
            throw new RuntimeException(e);
        }
    }

    public static RSAPrivateKey loadPrivateKey(String privateKeyStr) throws Exception {
        try {
            byte[] buffer = base64Decode(privateKeyStr);
            PKCS8EncodedKeySpec keySpec = new PKCS8EncodedKeySpec(buffer);
            KeyFactory keyFactory = KeyFactory.getInstance(RSA_ALGORITHM);
            return (RSAPrivateKey) keyFactory.generatePrivate(keySpec);
        } catch (NoSuchAlgorithmException e) {
            throw new RuntimeException(e);
        } catch (InvalidKeySpecException e) {
            throw new RuntimeException(e);
        }
    }

    public void savePublicKey(PublicKey publicKey) throws IOException {
        // 得到公钥字符串
        String publicKeyString = base64Encode(publicKey.getEncoded());
        System.out.println("publicKeyString="+publicKeyString);
        FileWriter fw = new FileWriter("publicKey.keystore");
        BufferedWriter bw = new BufferedWriter(fw);
        bw.write(publicKeyString);
        bw.close();
    }

    public void savePrivateKey(PrivateKey privateKey) throws IOException {
        // 得到私钥字符串
        String privateKeyString = base64Encode(privateKey.getEncoded());
        System.out.println("privateKeyString="+privateKeyString);

        BufferedWriter bw = new BufferedWriter(new FileWriter("privateKey.keystore"));
        bw.write(privateKeyString);
        bw.close();
    }

    public static String base64Encode(byte[] data) {
        return new BASE64Encoder().encode(data);
    }
    public static byte[] base64Decode(String data) throws IOException {
        return new BASE64Decoder().decodeBuffer(data);
    }

    public static void main(String [] args) throws Exception {
        // generate public and private keys
        KeyPair keyPair = buildKeyPair();
        PublicKey publicKey = keyPair.getPublic();
        PrivateKey privateKey = keyPair.getPrivate();

        // encrypt the message
        byte [] encrypted = encrypt(publicKey, "This is a secret message");
        System.out.println(base64Encode(encrypted));  // <<encrypted message>>

        // decrypt the message
        byte[] secret = decrypt(privateKey, encrypted);
        System.out.println(new String(secret, UTF8));     // This is a secret message
    }
}
