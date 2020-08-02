package com.cpdms.util;

import com.cpdms.common.conf.V2Config;
import org.springframework.core.io.ClassPathResource;
import org.springframework.core.io.Resource;
import org.springframework.util.ClassUtils;

import java.io.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.zip.ZipEntry;
import java.util.zip.ZipFile;
import java.util.zip.ZipInputStream;

public class ZipUtils {

    public static List<String> ZipContraMultiFile(String zippath){
        String outzippath  = ClassUtils.getDefaultClassLoader().getResource("").getPath();
        outzippath = outzippath + V2Config.getPersonnel_dir();
        List result = new ArrayList();
        try {
            Resource resource = new ClassPathResource(zippath);
            File file = resource.getFile();
            File outFile = null;
            ZipFile zipFile = new ZipFile(file);
            ZipInputStream zipInput = new ZipInputStream(new FileInputStream(file));
            ZipEntry entry = null;
            InputStream input = null;
            OutputStream output = null;
            while((entry = zipInput.getNextEntry()) != null){
                if (!entry.isDirectory() && !entry.getName().startsWith("_") && (entry.getName().endsWith("jpg") || entry.getName().endsWith("xls") || entry.getName().endsWith("xlsx"))) {
                    String outFileName = outzippath + File.separator + entry.getName();
                    result.add(outFileName);
                    outFile = new File(outFileName);
                    if(!outFile.getParentFile().exists()){
                        outFile.getParentFile().mkdir();
                    }
                    if(!outFile.exists()){
                        outFile.createNewFile();
                    }
                    input = zipFile.getInputStream(entry);
                    output = new FileOutputStream(outFile);
                    int temp = 0;
                    while((temp = input.read()) != -1){
                        output.write(temp);
                    }
                    input.close();
                    output.close();
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        return result;
    }
}
