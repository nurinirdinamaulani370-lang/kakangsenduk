                <?php if (count($hasil) === 0): ?>

                    <div class="alert alert-info mb-0">
                        Belum ada data kontestan untuk ditampilkan.
                    </div>

                <?php else: ?>

                    <?php foreach (['Kakang', 'Senduk'] as $kategori): ?>

                        <?php
                        $dataKategori = array_values(array_filter(
                            $hasil,
                            function ($item) use ($kategori) {
                                return strcasecmp($item['kategori'], $kategori) === 0;
                            }
                        ));
                        ?>

                        <h6 class="category-heading">
                            Kategori <?= e($kategori) ?>
                        </h6>

                        <?php if (count($dataKategori) === 0): ?>

                            <div class="alert alert-light border">
                                Belum ada kontestan kategori <?= e($kategori) ?>.
                            </div>

                        <?php else: ?>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Foto</th>
                                            <th>Nomor Peserta</th>
                                            <th>Nama Kontestan</th>
                                            <th>Asal Instansi</th>
                                            <th>Jumlah Suara</th>
                                            <th>Persentase</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($dataKategori as $index => $item): ?>

                                            <?php
                                            $jumlahSuara = (int) $item['jumlah_suara'];
                                            $totalKategori = $kategori === 'Kakang'
                                                ? $totalKakang
                                                : $totalSenduk;

                                            $persentase = $totalKategori > 0
                                                ? ($jumlahSuara / $totalKategori) * 100
                                                : 0;

                                            $tertinggi = $suaraTertinggi[$kategori] ?? 0;
                                            $isTeratas = $jumlahSuara > 0
                                                && $jumlahSuara === $tertinggi;
                                            ?>

                                            <tr>
                                                <td><?= $index + 1 ?></td>

                                                <td>
                                                    <?php if (!empty($item['foto'])): ?>
                                                        <img
                                                            src="../uploads/<?= rawurlencode(basename($item['foto'])) ?>"
                                                            alt="Foto <?= e($item['nama']) ?>"
                                                            class="candidate-photo"
                                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                        >
                                                        <div class="photo-placeholder" style="display:none;">👑</div>
                                                    <?php else: ?>
                                                        <div class="photo-placeholder">👑</div>
                                                    <?php endif; ?>
                                                </td>

                                                <td><?= e($item['nomor_peserta']) ?></td>
                                                <td class="fw-semibold"><?= e($item['nama']) ?></td>
                                                <td><?= e($item['asal_instansi']) ?></td>
                                                <td>
                                                    <strong><?= number_format($jumlahSuara) ?></strong>
                                                </td>
                                                <td style="min-width:130px;">
                                                    <div class="d-flex justify-content-between small mb-1">
                                                        <span><?= number_format($persentase, 1) ?>%</span>
                                                    </div>
                                                    <div class="progress">
                                                        <div class="progress-bar"
                                                             style="width: <?= $persentase ?>%;"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($isTeratas): ?>
                                                        <span class="badge badge-kakang rounded-pill">
                                                            Suara Terbanyak
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-secondary small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php endif; ?>

                    <?php endforeach; ?>

                <?php endif; ?>
            </div>

            <p class="text-secondary small mt-4">
                Catatan: Hasil ditampilkan berdasarkan seluruh suara yang tersimpan di database.
            </p>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
